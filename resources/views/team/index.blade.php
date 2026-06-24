@extends('layouts.app')

@section('title', 'Team Sheet - Teamiy Connect')
@section('page', 'team')
@section('page_title', 'Team Sheet')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/team.css') }}">
@endpush

@section('content')
    <div class="wrap">
        <section class="hero"><div class="blob"></div><div class="z"><div class="date">{{ $employee->company->name ?? 'Company' }}</div><div class="greet">Team Sheet</div><div class="summary">Employees in your company, prioritized by your department.</div></div></section>
        <div class="cards-grid auto-250" style="margin-top:18px">
            @forelse($members as $member)
                @php
                    $initials = collect(explode(' ', $member->name))->filter()->map(fn ($part) => strtoupper(substr($part, 0, 1)))->take(2)->implode('');
                @endphp
                <div class="card card-pad row">
                    <div class="avatar av-c{{ $loop->index % 6 }}" style="width:46px;height:46px">{{ $initials }}</div>
                    <div style="min-width:0">
                        <div style="font-weight:800;color:#1E293B">{{ $member->name }}</div>
                        <div style="font-size:12.5px;color:#64748B">{{ $member->post->post_name ?? 'Employee' }}</div>
                        <div style="font-size:12px;color:#94A3B8">{{ $member->department->dept_name ?? '-' }} · {{ $member->branch->name ?? '-' }}</div>
                    </div>
                </div>
            @empty
                @include('partials.empty-state', ['message' => 'No team members found.'])
            @endforelse
        </div>
        <div style="margin-top:18px">{{ $members->links() }}</div>
    </div>
@endsection
