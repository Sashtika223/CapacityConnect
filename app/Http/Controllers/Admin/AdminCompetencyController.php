<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Competency;
use App\Models\Department;
use App\Models\Skill;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminCompetencyController extends Controller
{
    public function index(): View
    {
        $competencies = Competency::with(['department', 'courses', 'users'])->paginate(15);
        $skills = Skill::withCount('users')->get();
        $departments = Department::orderBy('name')->get();

        return view('admin.competencies.index', compact('competencies', 'skills', 'departments'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => 'required|string|unique:competencies,code|max:50',
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'required_level' => 'required|integer|min:1|max:5',
            'department_id' => 'nullable|exists:departments,id',
            'description' => 'nullable|string',
        ]);

        $comp = Competency::create($request->all());

        AuditLogService::log('competency.created', $comp);

        return back()->with('success', 'Competency framework item created successfully.');
    }

    public function storeSkill(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => 'required|string|unique:skills,code|max:50',
            'name' => 'required|string|unique:skills,name|max:255',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);

        $skill = Skill::create($request->all());

        AuditLogService::log('skill.created', $skill);

        return back()->with('success', 'Skill added to global repository.');
    }
}
