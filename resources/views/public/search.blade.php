@extends('layouts.public')

@section('title', 'Global Search')

@section('content')
<div class="app-container" style="max-width:1100px;padding-top:42px;padding-bottom:64px;">
    <h1>Search Capacity Connect</h1>
    <form method="GET" action="{{ route('search') }}" style="display:flex;gap:12px;flex-wrap:wrap;margin:24px 0;">
        <input name="q" value="{{ $query }}" class="form-control" placeholder="Courses, trainers, skills, competencies, resources" required style="flex:1;min-width:280px;">
        <select name="category" class="form-control"><option value="">All categories</option><option value="Disaster Management">Disaster Management</option><option value="Response">Response</option></select>
        <select name="difficulty" class="form-control"><option value="">All levels</option><option>Beginner</option><option>Intermediate</option><option>Advanced</option><option>Expert</option></select>
        <button class="btn btn-primary" type="submit">Search</button>
    </form>
    @if($query)
        @foreach(['courses' => 'Courses', 'trainers' => 'Trainers', 'skills' => 'Skills', 'competencies' => 'Competencies', 'resources' => 'Learning Resources', 'announcements' => 'Announcements'] as $key => $heading)
            @if($$key->isNotEmpty())
                <section style="margin-top:28px;"><h2>{{ $heading }}</h2><div style="display:grid;gap:10px;margin-top:12px;">
                    @foreach($$key as $result)
                        <div class="card" style="padding:16px;"><strong>{{ $result->title ?? $result->name }}</strong><p style="margin:5px 0 0;color:var(--cc-text-muted);">{{ $result->short_description ?? $result->description ?? $result->content ?? '' }}</p></div>
                    @endforeach
                </div></section>
            @endif
        @endforeach
    @endif
</div>
@endsection