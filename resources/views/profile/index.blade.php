@extends('layouts.app')

@section('title', 'Profile Settings - Teamiy Connect')
@section('page', 'profile')
@section('page_title', 'Profile Settings')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endpush

@section('content')
    @php
        $initials = collect(explode(' ', $employee->name))->filter()->map(fn ($part) => strtoupper(substr($part, 0, 1)))->take(2)->implode('');
    @endphp
    <div class="wrap-sm">
        <div class="card" style="overflow:hidden">
            <div class="profile-cover"></div>
            <div class="card-pad-lg row" style="align-items:flex-end;margin-top:-48px">
                <div class="profile-av">{{ $initials }}</div>
                <div>
                    <h2 style="font-size:24px;font-weight:800;color:#1E293B">{{ $employee->name }}</h2>
                    <div style="font-size:13px;color:#64748B">{{ $employee->post->post_name ?? 'Employee' }} · {{ $employee->employee_code ?? 'No code' }}</div>
                </div>
            </div>
            <div class="info-grid">
                <div><div class="k">Work Email</div><div class="v">{{ $employee->work_email }}</div></div>
                <div><div class="k">Phone</div><div class="v">{{ $employee->phone ?: '-' }}</div></div>
                <div><div class="k">Company</div><div class="v">{{ $employee->company->name ?? '-' }}</div></div>
                <div><div class="k">Branch</div><div class="v">{{ $employee->branch->name ?? '-' }}</div></div>
                <div><div class="k">Department</div><div class="v">{{ $employee->department->dept_name ?? '-' }}</div></div>
                <div><div class="k">Office Time</div><div class="v">{{ $employee->officeTime?->opening_time }} - {{ $employee->officeTime?->closing_time }}</div></div>
                <div><div class="k">Joining Date</div><div class="v">{{ optional($employee->joining_date)->format('d M Y') ?: '-' }}</div></div>
                <div><div class="k">Contract</div><div class="v">{{ $employee->contract_type ?: '-' }}</div></div>
            </div>
        </div>

        <div class="cards-grid auto-250" style="margin-top:18px">
            <div class="card card-pad"><div class="section-title">Salary Account</div><div style="font-size:13px;color:#64748B;margin-top:8px">{{ $employee->employeeAccount->bank_name ?? 'No bank found' }}</div><div style="font-size:13px;color:#94A3B8;margin-top:4px">{{ $employee->employeeAccount->salary_cycle ?? '-' }}</div></div>
            <div class="card card-pad"><div class="section-title">Supervisor</div><div style="font-size:13px;color:#64748B;margin-top:8px">{{ $employee->supervisor->name ?? 'No supervisor assigned' }}</div></div>
        </div>
    </div>
@endsection
