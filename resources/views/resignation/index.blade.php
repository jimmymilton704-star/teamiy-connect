@extends('layouts.app')

@section('title', 'Resignation - Teamiy Connect')
@section('page', 'resignation')
@section('page_title', 'Resignation')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/resignation.css') }}">
@endpush

@section('content')
    <div class="wrap">
        <section class="hero"><div class="blob"></div><div class="z"><div class="date">Employee lifecycle</div><div class="greet">Resignation & HR Actions</div><div class="summary">Your resignation, transfer, and termination records.</div></div></section>
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

        <div class="card card-pad" style="margin-top:18px">
            <h3 class="section-title">Create Resignation Request</h3>
            <form method="POST" action="{{ route('resignation.store') }}" style="display:grid;gap:12px;margin-top:14px">
                @csrf
                <div class="cards-grid auto-250">
                    <input type="date" name="resignation_date" value="{{ old('resignation_date') }}" style="width:100%;padding:11px 12px;border:1px solid #E2E8F0;border-radius:8px">
                    <input type="date" name="last_working_day" value="{{ old('last_working_day') }}" style="width:100%;padding:11px 12px;border:1px solid #E2E8F0;border-radius:8px">
                </div>
                <textarea name="reason" rows="4" placeholder="Reason" style="width:100%;padding:11px 12px;border:1px solid #E2E8F0;border-radius:8px">{{ old('reason') }}</textarea>
                <button class="hero-btn" type="submit">Submit Request</button>
            </form>
        </div>

        <div class="card" style="margin-top:18px">
            <div class="card-pad-lg"><h3 class="section-title">Resignation Requests</h3></div>
            <div class="table-wrap">
                <table class="table"><thead><tr><th>Submitted</th><th>Last Working Day</th><th>Status</th><th>Reason</th><th>Admin Remark</th></tr></thead><tbody>
                    @forelse($resignations as $resignation)
                        <tr><td>{{ optional($resignation->resignation_date)->format('d M Y') }}</td><td>{{ optional($resignation->last_working_day)->format('d M Y') }}</td><td>@include('partials.status-badge', ['slot' => $resignation->status])</td><td>{{ $resignation->reason ?: '-' }}</td><td>{{ $resignation->admin_remark ?: '-' }}</td></tr>
                    @empty
                        <tr><td colspan="5">No resignation requests found.</td></tr>
                    @endforelse
                </tbody></table>
            </div>
        </div>
        <div class="cards-grid auto-330" style="margin-top:18px">
            @foreach($transfers as $transfer)
                <div class="card card-pad"><div class="spread"><strong>Transfer</strong>@include('partials.status-badge', ['slot' => $transfer->status])</div><div style="font-size:13px;color:#64748B;margin-top:8px">{{ optional($transfer->transfer_date)->format('d M Y') }}</div><div style="font-size:13px;color:#94A3B8;margin-top:4px">{{ $transfer->description }}</div></div>
            @endforeach
            @foreach($terminations as $termination)
                <div class="card card-pad"><div class="spread"><strong>Termination</strong>@include('partials.status-badge', ['slot' => $termination->status])</div><div style="font-size:13px;color:#64748B;margin-top:8px">{{ optional($termination->termination_date)->format('d M Y') }}</div><div style="font-size:13px;color:#94A3B8;margin-top:4px">{{ $termination->reason }}</div></div>
            @endforeach
        </div>
    </div>
@endsection
