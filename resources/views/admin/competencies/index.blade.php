@extends('layouts.admin')

@section('title', 'Competency & Skill Framework')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">
    <!-- Top Header -->
    <div style="background: #1e293b; padding: 24px; border-radius: 14px; border: 1px solid #334155; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 24px; font-weight: 700; color: #f8fafc; margin: 0 0 4px 0;">⚡ Competency & Skill Catalog</h1>
            <p style="font-size: 14px; color: #94a3b8; margin: 0;">Define emergency workforce competency frameworks, required proficiency levels, and skill taxonomies.</p>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        <!-- Left: Competencies Table & Add Form -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 24px;">
                <h3 style="font-size: 18px; font-weight: 700; color: #f8fafc; margin: 0 0 16px 0;">
                    📜 Global Competencies Catalog
                </h3>

                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
                        <thead>
                            <tr style="border-bottom: 1px solid #334155; color: #94a3b8;">
                                <th style="padding: 12px;">Code / Title</th>
                                <th style="padding: 12px;">Category</th>
                                <th style="padding: 12px;">Required Level</th>
                                <th style="padding: 12px;">Linked Courses</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($competencies as $comp)
                                <tr style="border-bottom: 1px solid #0f172a; color: #e2e8f0;">
                                    <td style="padding: 12px; font-weight: 700;">
                                        <div style="color: #f8fafc;">{{ $comp->title }}</div>
                                        <div style="font-size: 11px; color: #38bdf8; font-family: monospace;">CODE: {{ $comp->code }}</div>
                                    </td>
                                    <td style="padding: 12px; color: #cbd5e1;">{{ $comp->category }}</td>
                                    <td style="padding: 12px;">
                                        <span class="badge" style="background: rgba(59,130,246,0.2); color: #60a5fa; font-weight: 700;">
                                            Level {{ $comp->required_level }} / 5
                                        </span>
                                    </td>
                                    <td style="padding: 12px; color: #10b981; font-weight: 700;">
                                        {{ $comp->courses->count() }} Courses
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="padding: 20px; text-align: center; color: #64748b;">No competencies created yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div style="margin-top: 12px;">
                    {{ $competencies->links() }}
                </div>
            </div>

            <!-- Add Competency Form -->
            <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 24px;">
                <h3 style="font-size: 18px; font-weight: 700; color: #f8fafc; margin: 0 0 16px 0;">+ Register New Competency Item</h3>

                <form action="{{ route('admin.competencies.store') }}" method="POST" style="display: flex; flex-direction: column; gap: 14px;">
                    @csrf
                    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 12px;">
                        <input type="text" name="code" placeholder="Competency Code (e.g. COMP-FLD-01)" required style="background: #0f172a; border: 1px solid #334155; border-radius: 8px; padding: 10px; color: #fff; font-size: 13px;">
                        <input type="text" name="title" placeholder="Competency Title (e.g. Rapid Shelter Logistics)" required style="background: #0f172a; border: 1px solid #334155; border-radius: 8px; padding: 10px; color: #fff; font-size: 13px;">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px;">
                        <input type="text" name="category" placeholder="Category (e.g. Disaster Response)" required style="background: #0f172a; border: 1px solid #334155; border-radius: 8px; padding: 10px; color: #fff; font-size: 13px;">
                        <select name="required_level" required style="background: #0f172a; border: 1px solid #334155; border-radius: 8px; padding: 10px; color: #fff; font-size: 13px;">
                            <option value="1">Level 1 (Novice)</option>
                            <option value="2">Level 2 (Basic)</option>
                            <option value="3" selected>Level 3 (Proficient)</option>
                            <option value="4">Level 4 (Advanced)</option>
                            <option value="5">Level 5 (Expert Command)</option>
                        </select>
                        <select name="department_id" style="background: #0f172a; border: 1px solid #334155; border-radius: 8px; padding: 10px; color: #fff; font-size: 13px;">
                            <option value="">All Departments</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <textarea name="description" rows="2" placeholder="Description & observable performance indicators..." style="background: #0f172a; border: 1px solid #334155; border-radius: 8px; padding: 10px; color: #fff; font-size: 13px;"></textarea>
                    <button type="submit" class="btn btn-primary" style="background: #2563eb; color: #fff; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 700; font-size: 13px; width: fit-content;">
                        Create Competency ➔
                    </button>
                </form>
            </div>
        </div>

        <!-- Right: Skills Repository -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 20px;">
                <h3 style="font-size: 16px; font-weight: 700; color: #f8fafc; margin: 0 0 14px 0;">
                    🛠 Global Skills Repository
                </h3>

                <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px;">
                    @foreach($skills as $sk)
                        <span class="badge" style="background: #0f172a; border: 1px solid #334155; color: #38bdf8; font-size: 11px;">
                            {{ $sk->name }} ({{ $sk->users_count }})
                        </span>
                    @endforeach
                </div>

                <form action="{{ route('admin.skills.store') }}" method="POST" style="background: #0f172a; padding: 14px; border-radius: 10px; border: 1px solid #334155; display: flex; flex-direction: column; gap: 10px;">
                    @csrf
                    <div style="font-size: 13px; font-weight: 700; color: #f8fafc;">+ Add New Skill</div>
                    <input type="text" name="code" placeholder="Skill Code (e.g. SKL-HAZ-01)" required style="background: #1e293b; border: 1px solid #334155; border-radius: 6px; padding: 8px; color: #fff; font-size: 12px;">
                    <input type="text" name="name" placeholder="Skill Name" required style="background: #1e293b; border: 1px solid #334155; border-radius: 6px; padding: 8px; color: #fff; font-size: 12px;">
                    <input type="text" name="category" placeholder="Category" value="Operations" required style="background: #1e293b; border: 1px solid #334155; border-radius: 6px; padding: 8px; color: #fff; font-size: 12px;">
                    <button type="submit" class="btn btn-secondary" style="background: #334155; color: #fff; border: none; padding: 6px 12px; border-radius: 6px; font-weight: 600; font-size: 12px;">
                        Add Skill
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
