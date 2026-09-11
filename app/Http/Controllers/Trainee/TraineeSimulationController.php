<?php

namespace App\Http\Controllers\Trainee;

use App\Http\Controllers\Controller;
use App\Models\SimulationAttempt;
use App\Models\SimulationScenario;
use App\Services\AuditLogService;
use App\Services\SimulationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TraineeSimulationController extends Controller
{
    public function __construct(
        protected SimulationService $simulationService
    ) {}

    public function index(): View
    {
        $scenarios = SimulationScenario::with('targetCompetency')
            ->where('is_active', true)
            ->get();

        $userAttempts = Auth::user()->simulationAttempts()->with('scenario')->latest()->get();

        return view('trainee.simulations.index', compact('scenarios', 'userAttempts'));
    }

    public function show(SimulationScenario $scenario): View
    {
        $attempt = $this->simulationService->startAttempt(Auth::user(), $scenario);
        $treeData = $scenario->decision_tree_data ?? [];
        $currentNode = $treeData[$attempt->current_node] ?? null;

        return view('trainee.simulations.run', compact('scenario', 'attempt', 'currentNode'));
    }

    public function decide(Request $request, SimulationScenario $scenario, SimulationAttempt $attempt): JsonResponse|RedirectResponse
    {
        if ($attempt->user_id !== Auth::id()) {
            abort(403);
        }

        $choiceId = $request->input('choice_id');
        $result = $this->simulationService->submitDecision($attempt, $choiceId);

        AuditLogService::log('simulation.decision', $attempt, null, [
            'scenario' => $scenario->title,
            'choice' => $choiceId,
            'score' => $result['total_score'] ?? 0,
        ]);

        if ($request->wantsJson()) {
            return response()->json($result);
        }

        return redirect()->route('trainee.simulations.show', $scenario->id);
    }
}
