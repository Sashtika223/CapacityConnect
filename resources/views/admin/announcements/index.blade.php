@extends('layouts.admin')

@section('title', 'Manage Broadcast Bulletins')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">
    <!-- Header -->
    <div style="background: #1e293b; padding: 24px; border-radius: 14px; border: 1px solid #334155; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <span class="badge" style="background: rgba(245,158,11,0.2); color: #f59e0b; border: 1px solid rgba(245,158,11,0.4); margin-bottom: 6px; display: inline-block;">
                📢 Communications & Alerts
            </span>
            <h1 style="font-size: 24px; font-weight: 800; color: #f8fafc; margin: 0 0 4px 0;">Platform Bulletins & Announcements</h1>
            <p style="font-size: 14px; color: #94a3b8; margin: 0;">Publish high-priority operational updates, protocol revisions, and alerts to trainees and trainers.</p>
        </div>

        <button onclick="document.getElementById('create-ann-modal').style.display = 'block'" class="btn btn-primary" style="background: #2563eb; color: #fff; padding: 10px 20px; border-radius: 8px; font-weight: 700; border: none; cursor: pointer;">
            + Post Bulletin
        </button>
    </div>

    <!-- Create Announcement Form Modal -->
    <div id="create-ann-modal" style="display: none; background: #1e293b; border: 1px solid #334155; border-radius: 14px; padding: 24px;">
        <h3 style="font-size: 18px; font-weight: 700; color: #f8fafc; margin: 0 0 16px 0;">Publish State Emergency Bulletin</h3>
        <form action="{{ route('admin.announcements.store') }}" method="POST">
            @csrf
            <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-size: 12px; color: #94a3b8; margin-bottom: 4px;">Bulletin Title</label>
                    <input type="text" name="title" required class="form-control" style="width: 100%; padding: 10px; background: #0f172a; border: 1px solid #334155; border-radius: 8px; color: #fff;" placeholder="e.g. Cyclone Season Readiness Briefing">
                </div>
                <div>
                    <label style="display: block; font-size: 12px; color: #94a3b8; margin-bottom: 4px;">Target Audience</label>
                    <select name="target_role" required class="form-control" style="width: 100%; padding: 10px; background: #0f172a; border: 1px solid #334155; border-radius: 8px; color: #fff;">
                        <option value="all">All Users</option>
                        <option value="trainee">Trainees Only</option>
                        <option value="trainer">Trainers Only</option>
                    </select>
                </div>
                <label style="display: flex; gap: 8px; align-items: center; color: #cbd5e1; font-size: 12px; padding-top: 24px;">
                    <input type="checkbox" name="is_pinned" value="1">
                    Pin bulletin
                </label>
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 12px; color: #94a3b8; margin-bottom: 4px;">Bulletin Content</label>
                <textarea name="content" required rows="3" class="form-control" style="width: 100%; padding: 10px; background: #0f172a; border: 1px solid #334155; border-radius: 8px; color: #fff;" placeholder="Write bulletin announcement..."></textarea>
            </div>
            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <button type="button" onclick="document.getElementById('create-ann-modal').style.display = 'none'" class="btn" style="background: transparent; color: #94a3b8; border: 1px solid #334155; padding: 8px 16px; border-radius: 6px; cursor: pointer;">Cancel</button>
                <button type="submit" class="btn btn-primary" style="background: #2563eb; color: #fff; padding: 8px 20px; border-radius: 6px; border: none; font-weight: 700; cursor: pointer;">Publish Bulletin</button>
            </div>
        </form>
    </div>

    <!-- Announcements List -->
    <div style="display: flex; flex-direction: column; gap: 12px;">
        @forelse($announcements as $ann)
            <div style="background: #1e293b; padding: 20px; border-radius: 12px; border: 1px solid #334155; display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                        <span class="badge" style="background: {{ $ann->is_pinned ? '#ef4444' : '#3b82f6' }}; color: #fff; font-size: 10px; font-weight: 700; text-transform: uppercase;">
                            {{ $ann->is_pinned ? 'Pinned' : 'Bulletin' }}
                        </span>
                        <span style="font-size: 11px; color: #94a3b8;">Audience: <strong>{{ strtoupper($ann->target_role) }}</strong> • Published: {{ $ann->published_at?->diffForHumans() }}</span>
                    </div>
                    <h3 style="font-size: 16px; font-weight: 700; color: #f8fafc; margin: 4px 0 6px 0;">{{ $ann->title }}</h3>
                    <p style="font-size: 13px; color: #cbd5e1; margin: 0; line-height: 1.5;">{{ $ann->content }}</p>
                </div>
                <form action="{{ route('admin.announcements.destroy', $ann->id) }}" method="POST" onsubmit="return confirm('Remove announcement?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="background: none; border: none; color: #f87171; cursor: pointer; font-size: 12px; font-weight: 700;">Delete</button>
                </form>
            </div>
        @empty
            <p style="color: #64748b; font-size: 13px;">No announcements posted.</p>
        @endforelse
    </div>
</div>
@endsection
