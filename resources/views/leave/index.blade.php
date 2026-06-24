@extends('layouts.app')

@section('title', 'Leave Management - Teamiy Connect')
@section('page', 'leave')
@section('page_title', 'Leave Management')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/leave.css') }}">
@endpush

@section('content')
    <div class="wrap">
        <section class="hero">
            <div class="blob"></div>
            <div class="z">
                <div class="date">Leave balance</div>
                <div class="greet">{{ $employee->leave_allocated ?? 0 }} days allocated</div>
                <div class="summary">Leave types, requests, and short time-leave records from HR.</div>
            </div>
        </section>

        <div class="cards-grid auto-250" style="margin-top:18px">
            @forelse($leaveTypes as $leaveType)
                <div class="card card-pad">
                    <div class="spread">
                        <span class="lbl">{{ $leaveType->leaveType->name ?? 'Leave type' }}</span>
                        <span class="badge badge-blue">{{ $leaveType->days }} days</span>
                    </div>
                    <div style="font-size:12.5px;color:#94A3B8;margin-top:8px">
                        {{ $leaveType->early_exit ? 'Early exit enabled' : 'Standard leave' }}
                    </div>
                </div>
            @empty
                @include('partials.empty-state', ['message' => 'No leave allocation found.'])
            @endforelse
        </div>

        @if(session('status'))
            <div class="card card-pad" style="margin-top:18px;color:#0F766E;background:#ECFDF5;border-color:#A7F3D0">
                {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="card card-pad" style="margin-top:18px;color:#B91C1C;background:#FEF2F2;border-color:#FECACA">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="cards-grid auto-330" style="margin-top:18px">
            <div class="card card-pad">
                <h3 class="section-title">Create Leave Request</h3>
                <form method="POST" action="{{ route('leave.store') }}" style="display:grid;gap:12px;margin-top:14px">
                    @csrf
                    <input name="title" value="{{ old('title') }}" placeholder="Title" style="width:100%;padding:11px 12px;border:1px solid #E2E8F0;border-radius:8px">
                    <select name="leave_type_id" style="width:100%;padding:11px 12px;border:1px solid #E2E8F0;border-radius:8px;background:#fff">
                        <option value="">Leave type</option>
                        @foreach($leaveTypes as $employeeLeaveType)
                            @php($leaveType = $employeeLeaveType->leaveType)
                            @if($leaveType)
                                <option value="{{ $leaveType->id }}" @selected(old('leave_type_id') == $leaveType->id)>{{ $leaveType->name }}</option>
                            @endif
                        @endforeach
                    </select>
                    <div class="cards-grid auto-250">
                        <input type="date" name="leave_from" value="{{ old('leave_from') }}" style="width:100%;padding:11px 12px;border:1px solid #E2E8F0;border-radius:8px">
                        <input type="date" name="leave_to" value="{{ old('leave_to') }}" style="width:100%;padding:11px 12px;border:1px solid #E2E8F0;border-radius:8px">
                    </div>
                    <textarea name="reasons" rows="4" placeholder="Reason" style="width:100%;padding:11px 12px;border:1px solid #E2E8F0;border-radius:8px">{{ old('reasons') }}</textarea>
                    <button class="hero-btn" type="submit">Submit Request</button>
                </form>
            </div>

            <div class="card card-pad">
                <h3 class="section-title">Request Time Leave</h3>
                <form method="POST" action="{{ route('leave.time-leave.store') }}" style="display:grid;gap:12px;margin-top:14px">
                    @csrf
                    <input type="date" name="issue_date" value="{{ old('issue_date') }}" style="width:100%;padding:11px 12px;border:1px solid #E2E8F0;border-radius:8px">
                    <div class="cards-grid auto-250">
                        <input type="time" name="start_time" value="{{ old('start_time') }}" style="width:100%;padding:11px 12px;border:1px solid #E2E8F0;border-radius:8px">
                        <input type="time" name="end_time" value="{{ old('end_time') }}" style="width:100%;padding:11px 12px;border:1px solid #E2E8F0;border-radius:8px">
                    </div>
                    <textarea name="reasons" rows="4" placeholder="Reason" style="width:100%;padding:11px 12px;border:1px solid #E2E8F0;border-radius:8px">{{ old('reasons') }}</textarea>
                    <button class="hero-btn" type="submit">Submit Time Leave</button>
                </form>
            </div>
        </div>

        <div class="card" style="margin-top:18px">
            <div class="card-pad-lg"><h3 class="section-title">Leave Requests</h3></div>
            <div class="table-wrap">
                <table class="table">
                    <thead><tr><th>Title</th><th>Type</th><th>From</th><th>To</th><th>Days</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse($leaveRequests as $request)
                            <tr>
                                <td>{{ $request->title ?: 'Leave request' }}</td>
                                <td>{{ $request->leaveType->name ?? '-' }}</td>
                                <td>{{ optional($request->leave_from)->format('d M Y') ?? $request->leave_from }}</td>
                                <td>{{ optional($request->leave_to)->format('d M Y') ?? $request->leave_to }}</td>
                                <td>{{ $request->no_of_days }}</td>
                                <td>@include('partials.status-badge', ['slot' => $request->status])</td>
                            </tr>
                        @empty
                            <tr><td colspan="6">No leave requests found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-pad">{{ $leaveRequests->links() }}</div>
        </div>

        <div class="card" style="margin-top:18px">
            <div class="card-pad-lg"><h3 class="section-title">Time Leave Requests</h3></div>
            <div class="table-wrap">
                <table class="table">
                    <thead><tr><th>Date</th><th>Start</th><th>End</th><th>Status</th><th>Reason</th></tr></thead>
                    <tbody>
                        @forelse($timeLeaves as $timeLeave)
                            <tr>
                                <td>{{ optional($timeLeave->issue_date)->format('d M Y') ?? $timeLeave->issue_date }}</td>
                                <td>{{ $timeLeave->start_time }}</td>
                                <td>{{ $timeLeave->end_time }}</td>
                                <td>@include('partials.status-badge', ['slot' => $timeLeave->status])</td>
                                <td>{{ $timeLeave->reasons }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5">No time leave requests found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
