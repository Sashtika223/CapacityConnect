<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Competency;
use App\Models\Course;
use App\Models\Department;
use App\Models\User;
use App\Services\KnowledgeGraphService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminKnowledgeGraphController extends Controller
{
    public function __construct(
        protected KnowledgeGraphService $graphService
    ) {}

    public function index(): View
    {
        $graphData = $this->graphService->getGraphData();
        $departmentsCount = Department::count();
        $competenciesCount = Competency::count();
        $coursesCount = Course::where('status', 'published')->count();
        $expertsCount = User::whereIn('role', ['trainer', 'admin'])->count();

        return view('admin.knowledge-graph.index', compact('graphData', 'departmentsCount', 'competenciesCount', 'coursesCount', 'expertsCount'));
    }

    public function expertDiscovery(Request $request): View
    {
        $query = $request->input('query');
        $competencyId = $request->input('competency_id');

        $experts = User::whereIn('role', ['trainer', 'admin', 'trainee'])
            ->with(['department', 'profile', 'qualifications', 'experiences', 'userCompetencies.competency'])
            ->where('status', 'active');

        if ($competencyId) {
            $experts->whereHas('userCompetencies', function ($q) use ($competencyId) {
                $q->where('competency_id', $competencyId)->where('proficiency_level', '>=', 3);
            });
        }

        if ($query) {
            $experts->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhereHas('userCompetencies.competency', function ($cq) use ($query) {
                        $cq->where('title', 'like', "%{$query}%");
                    })
                    ->orWhereHas('skills', function ($sq) use ($query) {
                        $sq->where('name', 'like', "%{$query}%");
                    });
            });
        }

        $results = $experts->get()->map(function ($user) {
            // Transparent ranking score based on: competency levels (weight 40%), qualifications (20%), certificates (20%), experience (20%)
            $compScore = $user->userCompetencies->sum('proficiency_level') * 8;
            $qualScore = $user->qualifications->count() * 10;
            $certScore = $user->certificates()->where('status', 'valid')->count() * 15;
            $expScore = min(30, $user->experiences->count() * 10);
            $totalScore = min(100, $compScore + $qualScore + $certScore + $expScore);

            $user->expert_score = $totalScore;

            return $user;
        })->sortByDesc('expert_score');

        $competencies = Competency::orderBy('title')->get();

        return view('admin.knowledge-graph.expert-discovery', compact('results', 'competencies', 'query', 'competencyId'));
    }
}
