@extends('layouts.admin')

@section('title', 'Knowledge Graph')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">
    <!-- Top Header -->
    <div style="background: #1e293b; padding: 24px; border-radius: 14px; border: 1px solid #334155; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 24px; font-weight: 700; color: #f8fafc; margin: 0 0 4px 0;">🕸 Organizational Knowledge Graph</h1>
            <p style="font-size: 14px; color: #94a3b8; margin: 0;">Interactive visual graph interconnecting Personnel, Departments, Competencies, Courses, and Assessments.</p>
        </div>

        <a href="{{ route('admin.expert-discovery') }}" class="btn btn-primary" style="background: #2563eb; color: #fff; text-decoration: none; padding: 10px 18px; border-radius: 8px; font-weight: 700; font-size: 13px;">
            🔍 Hidden Expert Discovery
        </a>
    </div>

    <!-- Graph Canvas Card -->
    <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3 style="font-size: 16px; font-weight: 700; color: #f8fafc; margin: 0;">Interactive Entity Graph</h3>
            <div style="display: flex; gap: 14px; font-size: 12px;">
                <span style="color: #60a5fa;">● Departments ({{ $departmentsCount }})</span>
                <span style="color: #34d399;">● Competencies ({{ $competenciesCount }})</span>
                <span style="color: #f59e0b;">● Courses ({{ $coursesCount }})</span>
                <span style="color: #a78bfa;">● Personnel ({{ $expertsCount }})</span>
            </div>
        </div>

        <div id="knowledge-graph-canvas" style="width: 100%; height: 500px; background: #0f172a; border-radius: 12px; border: 1px solid #334155; display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden;">
            <svg id="knowledge-graph-svg" style="width: 100%; height: 100%;" role="img" aria-label="Knowledge graph relationships"></svg>
            <script>
                const graphData = @json($graphData);
                const svg = document.getElementById('knowledge-graph-svg');
                const width = 900;
                const height = 500;
                const positions = new Map(graphData.nodes.map((node, index) => [node.id, {
                    x: 70 + (index % 6) * 150,
                    y: 80 + (Math.floor(index / 6) % 3) * 170
                }]));
                const namespace = 'http://www.w3.org/2000/svg';

                graphData.links.forEach((link) => {
                    const source = positions.get(link.source);
                    const target = positions.get(link.target);
                    if (!source || !target) return;
                    const line = document.createElementNS(namespace, 'line');
                    line.setAttribute('x1', source.x);
                    line.setAttribute('y1', source.y);
                    line.setAttribute('x2', target.x);
                    line.setAttribute('y2', target.y);
                    line.setAttribute('stroke', '#7b8d98');
                    line.setAttribute('stroke-width', '1.5');
                    svg.appendChild(line);
                });

                graphData.nodes.forEach((node) => {
                    const position = positions.get(node.id);
                    const group = document.createElementNS(namespace, 'g');
                    const circle = document.createElementNS(namespace, 'circle');
                    circle.setAttribute('cx', position.x);
                    circle.setAttribute('cy', position.y);
                    circle.setAttribute('r', Math.max(20, Math.min(31, node.size + 5)));
                    circle.setAttribute('fill', node.color);
                    circle.setAttribute('stroke', '#ffffff');
                    circle.setAttribute('stroke-width', '2');
                    const label = document.createElementNS(namespace, 'text');
                    label.setAttribute('x', position.x);
                    label.setAttribute('y', position.y + 4);
                    label.setAttribute('fill', '#ffffff');
                    label.setAttribute('font-size', '10');
                    label.setAttribute('text-anchor', 'middle');
                    label.textContent = node.name.length > 18 ? node.name.slice(0, 16) + '...' : node.name;
                    group.append(circle, label);
                    svg.appendChild(group);
                });
            </script>
            <div style="position: absolute; bottom: 16px; left: 16px; background: rgba(15,23,42,0.85); padding: 8px 14px; border-radius: 6px; font-size: 11px; color: #94a3b8; border: 1px solid #334155;">
                Interconnected node map powered by KnowledgeGraphService
            </div>
        </div>
    </div>
</div>
@endsection
