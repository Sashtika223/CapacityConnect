<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Competency;
use App\Models\Department;
use App\Services\CapacitySimulatorService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminCapacitySimulatorController extends Controller
{
    public function __construct(
        protected CapacitySimulatorService $simulatorService
    ) {}

    public function index(Request $request): View
    {
        $departments = Department::orderBy('name')->get();
        $competencies = Competency::orderBy('title')->get();

        $params = [
            'department_id' => $request->input('department_id'),
            'competency_id' => $request->input('competency_id', $competencies->first()?->id),
            'workforce_count' => $request->input('workforce_count', 80),
            'batch_size' => $request->input('batch_size', 20),
            'completion_rate' => $request->input('completion_rate', 85),
            'target_level' => $request->input('target_level', 4),
        ];

        $simulationResult = $this->simulatorService->runSimulation($params);

        return view('admin.simulator.index', compact('departments', 'competencies', 'params', 'simulationResult'));
    }
}
