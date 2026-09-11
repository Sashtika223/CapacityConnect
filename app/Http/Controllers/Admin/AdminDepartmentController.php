<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDepartmentController extends Controller
{
    public function index(): View
    {
        $departments = Department::withCount(['users', 'courses', 'competencies'])->orderBy('name')->get();

        return view('admin.departments.index', compact('departments'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
            'code' => 'required|string|max:50|unique:departments,code',
            'description' => 'nullable|string',
            'head_of_department' => 'nullable|string|max:255',
        ]);

        $department = Department::create($validated);
        AuditLogService::log('department.created', $department, null, ['name' => $department->name]);

        return back()->with('success', "Department '{$department->name}' created successfully!");
    }

    public function update(Request $request, Department $department): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,'.$department->id,
            'code' => 'required|string|max:50|unique:departments,code,'.$department->id,
            'description' => 'nullable|string',
            'head_of_department' => 'nullable|string|max:255',
        ]);

        $department->update($validated);
        AuditLogService::log('department.updated', $department);

        return back()->with('success', "Department '{$department->name}' updated successfully!");
    }

    public function destroy(Department $department): RedirectResponse
    {
        $name = $department->name;
        $department->delete();
        AuditLogService::log('department.deleted', null, null, ['name' => $name]);

        return back()->with('info', "Department '{$name}' removed.");
    }
}
