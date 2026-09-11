<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Models\Competency;
use App\Models\Course;
use App\Models\Department;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TrainerCourseController extends Controller
{
    public function index(): View
    {
        $courses = Course::where('trainer_id', Auth::id())
            ->with(['department', 'modules.lessons', 'enrollments'])
            ->latest()
            ->paginate(10);

        return view('trainer.courses.index', compact('courses'));
    }

    public function create(): View
    {
        $departments = Department::orderBy('name')->get();
        $competencies = Competency::orderBy('title')->get();

        return view('trainer.courses.create', compact('departments', 'competencies'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'category' => 'required|string|max:100',
            'difficulty' => 'required|in:Beginner,Intermediate,Advanced,Expert',
            'duration_hours' => 'required|integer|min:1|max:500',
            'short_description' => 'required|string|max:500',
            'full_description' => 'required|string|min:50',
            'competency_ids' => 'nullable|array',
            'competency_ids.*' => 'exists:competencies,id',
            'target_levels' => 'nullable|array',
            'learning_objectives' => 'nullable|string',
        ]);

        $slug = Str::slug($request->input('title')).'-'.Str::random(5);

        $objectives = array_filter(array_map('trim', explode("\n", (string) $request->input('learning_objectives'))));

        $course = Course::create([
            'trainer_id' => Auth::id(),
            'department_id' => $request->input('department_id'),
            'title' => $request->input('title'),
            'slug' => $slug,
            'short_description' => $request->input('short_description'),
            'full_description' => $request->input('full_description'),
            'category' => $request->input('category'),
            'difficulty' => $request->input('difficulty'),
            'duration_hours' => $request->input('duration_hours'),
            'learning_objectives' => $objectives,
            'status' => 'draft',
        ]);

        if ($request->has('competency_ids')) {
            $syncData = [];
            $targetLevels = $request->input('target_levels', []);
            foreach ($request->input('competency_ids') as $compId) {
                $syncData[$compId] = ['target_level' => $targetLevels[$compId] ?? 3];
            }
            $course->competencies()->sync($syncData);
        }

        AuditLogService::log('course.created', $course);

        return redirect()->route('trainer.courses.builder', $course->id)
            ->with('success', 'Course draft created! Now add curriculum modules and lessons.');
    }

    public function edit(Course $course): View
    {
        $this->authorize('update', $course);

        $departments = Department::orderBy('name')->get();
        $competencies = Competency::orderBy('title')->get();
        $selectedCompetencyIds = $course->competencies()->pluck('competencies.id')->toArray();

        return view('trainer.courses.edit', compact('course', 'departments', 'competencies', 'selectedCompetencyIds'));
    }

    public function update(Request $request, Course $course): RedirectResponse
    {
        $this->authorize('update', $course);

        $request->validate([
            'title' => 'required|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'category' => 'required|string|max:100',
            'difficulty' => 'required|in:Beginner,Intermediate,Advanced,Expert',
            'duration_hours' => 'required|integer|min:1|max:500',
            'short_description' => 'required|string|max:500',
            'full_description' => 'required|string|min:50',
            'competency_ids' => 'nullable|array',
            'learning_objectives' => 'nullable|string',
        ]);

        $objectives = array_filter(array_map('trim', explode("\n", (string) $request->input('learning_objectives'))));

        $course->update([
            'department_id' => $request->input('department_id'),
            'title' => $request->input('title'),
            'short_description' => $request->input('short_description'),
            'full_description' => $request->input('full_description'),
            'category' => $request->input('category'),
            'difficulty' => $request->input('difficulty'),
            'duration_hours' => $request->input('duration_hours'),
            'learning_objectives' => $objectives,
        ]);

        if ($request->has('competency_ids')) {
            $course->competencies()->sync($request->input('competency_ids'));
        }

        AuditLogService::log('course.updated', $course);

        return back()->with('success', 'Course details updated successfully.');
    }

    public function submitForApproval(Course $course): RedirectResponse
    {
        $this->authorize('update', $course);

        if ($course->modules()->count() === 0) {
            return back()->with('error', 'Please add at least one module and lesson before submitting for approval.');
        }

        $course->update(['status' => 'pending']);

        AuditLogService::log('course.submitted_for_approval', $course);

        return back()->with('success', 'Course submitted for Admin approval.');
    }
}
