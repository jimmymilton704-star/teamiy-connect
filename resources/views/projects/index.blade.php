@extends('layouts.app')

@section('title', 'Projects - Teamiy Connect')
@section('page', 'projects')
@section('page_title', 'Projects')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/projects.css') }}">
@endpush

@section('content')
    <div class="wrap">
        <section class="hero"><div class="blob"></div><div class="z"><div class="date">My work</div><div class="greet">Projects</div><div class="summary">Projects and tasks connected to your team, branch, or direct assignments.</div></div></section>
        <div class="cards-grid auto-300" style="margin-top:18px">
            @forelse($projects as $project)
                <div class="card card-pad hover-pop">
                    <div class="spread"><h3 class="section-title">{{ $project->name }}</h3>@include('partials.status-badge', ['slot' => $project->status])</div>
                    <div style="font-size:13px;color:#64748B;margin-top:8px">{{ str($project->description)->stripTags()->limit(120) }}</div>
                    <div class="row flex-wrap" style="margin-top:14px;font-size:12.5px;color:#94A3B8">
                        <span>{{ optional($project->start_date)->format('d M Y') ?: '-' }}</span>
                        <span>Deadline {{ optional($project->deadline)->format('d M Y') ?: '-' }}</span>
                        <span>{{ $project->tasks_count }} tasks</span>
                    </div>
                </div>
            @empty
                @include('partials.empty-state', ['message' => 'No projects found.'])
            @endforelse
        </div>
        <div style="margin-top:18px">{{ $projects->links() }}</div>

        <div class="card" style="margin-top:18px">
            <div class="card-pad-lg"><h3 class="section-title">My Tasks</h3></div>
            <div class="table-wrap">
                <table class="table">
                    <thead><tr><th>Task</th><th>Project</th><th>Priority</th><th>Status</th><th>Due</th></tr></thead>
                    <tbody>
                        @forelse($tasks as $task)
                            <tr><td>{{ $task->name }}</td><td>{{ $task->project->name ?? '-' }}</td><td>{{ ucfirst($task->priority) }}</td><td>@include('partials.status-badge', ['slot' => $task->status])</td><td>{{ optional($task->end_date)->format('d M Y') ?: '-' }}</td></tr>
                        @empty
                            <tr><td colspan="5">No assigned tasks found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
