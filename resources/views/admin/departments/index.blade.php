@extends('layouts.admin')

@section('title', 'Manage Departments & Divisions')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">
    <!-- Header -->
    <div style="background: #1e293b; padding: 24px; border-radius: 14px; border: 1px solid #334155; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <span class="badge" style="background: rgba(59,130,246,0.2); color: #60a5fa; border: 1px solid rgba(59,130,246,0.4); margin-bottom: 6px; display: inline-block;">
                🏢 Organizational Structure
            </span>
            <h1 style="font-size: 24px; font-weight: 800; color: #f8fafc; margin: 0 0 4px 0;">State Emergency Departments</h1>
            <p style="font-size: 14px; color: #94a3b8; margin: 0;">Configure disaster response divisions, operational codes, and competency allocations.</p>
        </div>

        <button onclick="document.getElementById('create-dept-modal').style.display = 'block'" class="btn btn-primary" style="background: #2563eb; color: #fff; padding: 10px 20px; border-radius: 8px; font-weight: 700; border: none; cursor: pointer;">
            + Add New Department
        </button>
    </div>

    <!-- Create Department Modal -->
    <div id="create-dept-modal" style="display: none; background: #1e293b; border: 1px solid #334155; border-radius: 14px; padding: 24px;">
        <h3 style="font-size: 18px; font-weight: 700; color: #f8fafc; margin: 0 0 16px 0;">Create Disaster Response Department</h3>
        <form action="{{ route('admin.departments.store') }}" method="POST">
            @csrf
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-size: 12px; color: #94a3b8; margin-bottom: 4px;">Department Name</label>
                    <input type="text" name="name" required class="form-control" style="width: 100%; padding: 10px; background: #0f172a; border: 1px solid #334155; border-radius: 8px; color: #fff;" placeholder="e.g. Flood & Swiftwater Rescue Operations">
                </div>
                <div>
                    <label style="display: block; font-size: 12px; color: #94a3b8; margin-bottom: 4px;">Code</label>
                    <input type="text" name="code" required class="form-control" style="width: 100%; padding: 10px; background: #0f172a; border: 1px solid #334155; border-radius: 8px; color: #fff;" placeholder="e.g. FS-SRO">
                </div>
                <div>
                    <label style="display: block; font-size: 12px; color: #94a3b8; margin-bottom: 4px;">Head of Department</label>
                    <input type="text" name="head_of_department" class="form-control" style="width: 100%; padding: 10px; background: #0f172a; border: 1px solid #334155; border-radius: 8px; color: #fff;" placeholder="e.g. Inspector General of Rescue">
                </div>
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 12px; color: #94a3b8; margin-bottom: 4px;">Operational Mandate & Description</label>
                <textarea name="description" rows="2" class="form-control" style="width: 100%; padding: 10px; background: #0f172a; border: 1px solid #334155; border-radius: 8px; color: #fff;" placeholder="Primary operational scope and readiness goals..."></textarea>
            </div>
            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <button type="button" onclick="document.getElementById('create-dept-modal').style.display = 'none'" class="btn" style="background: transparent; color: #94a3b8; border: 1px solid #334155; padding: 8px 16px; border-radius: 6px; cursor: pointer;">Cancel</button>
                <button type="submit" class="btn btn-primary" style="background: #2563eb; color: #fff; padding: 8px 20px; border-radius: 6px; border: none; font-weight: 700; cursor: pointer;">Save Department</button>
            </div>
        </form>
    </div>

    <!-- Departments Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 16px;">
        @foreach($departments as $dept)
            <div style="background: #1e293b; padding: 22px; border-radius: 12px; border: 1px solid #334155; display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                        <span class="badge" style="background: rgba(59,130,246,0.2); color: #60a5fa; font-family: monospace; font-size: 12px;">{{ $dept->code }}</span>
                        <span style="font-size: 11px; color: #94a3b8;">{{ $dept->users_count }} Personnel</span>
                    </div>
                    <h3 style="font-size: 17px; font-weight: 700; color: #f8fafc; margin: 0 0 6px 0;">{{ $dept->name }}</h3>
                    <p style="font-size: 12px; color: #cbd5e1; margin: 0 0 12px 0; line-height: 1.4;">{{ $dept->description ?? 'Specialized emergency response division.' }}</p>
                </div>

                <div style="border-top: 1px solid #334155; padding-top: 12px; display: flex; justify-content: space-between; align-items: center; font-size: 11px; color: #94a3b8;">
                    <span>Courses: <strong style="color: #fff;">{{ $dept->courses_count }}</strong> • Competencies: <strong style="color: #fff;">{{ $dept->competencies_count }}</strong></span>
                    <form action="{{ route('admin.departments.destroy', $dept->id) }}" method="POST" onsubmit="return confirm('Remove department?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="background: none; border: none; color: #f87171; cursor: pointer; font-size: 11px; font-weight: 700;">Delete</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
