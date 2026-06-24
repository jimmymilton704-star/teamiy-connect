@extends('layouts.app')

@section('title', 'TADA - Teamiy Connect')
@section('page', 'tada')
@section('page_title', 'TADA')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/tada.css') }}">
@endpush

@section('content')
    <div class="wrap">
        <section class="hero"><div class="blob"></div><div class="z"><div class="date">Travel and daily allowance</div><div class="greet">My TADA Claims</div><div class="summary">Submitted expenses and approval status.</div></div></section>
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
            <h3 class="section-title">Create TADA Claim</h3>
            <form method="POST" action="{{ route('tada.store') }}" style="display:grid;gap:12px;margin-top:14px">
                @csrf
                <input name="title" value="{{ old('title') }}" placeholder="Claim title" style="width:100%;padding:11px 12px;border:1px solid #E2E8F0;border-radius:8px">
                <input type="number" step="0.01" min="0" name="total_expense" value="{{ old('total_expense') }}" placeholder="Total expense" style="width:100%;padding:11px 12px;border:1px solid #E2E8F0;border-radius:8px">
                <textarea name="description" rows="4" placeholder="Description" style="width:100%;padding:11px 12px;border:1px solid #E2E8F0;border-radius:8px">{{ old('description') }}</textarea>
                <button class="hero-btn" type="submit">Submit Claim</button>
            </form>
        </div>

        <div class="card" style="margin-top:18px">
            <div class="table-wrap">
                <table class="table">
                    <thead><tr><th>Title</th><th>Expense</th><th>Status</th><th>Approved</th><th>Remark</th></tr></thead>
                    <tbody>
                        @forelse($tadas as $tada)
                            <tr><td>{{ $tada->title }}</td><td>{{ number_format((float) $tada->total_expense, 2) }}</td><td>@include('partials.status-badge', ['slot' => $tada->status])</td><td>{{ optional($tada->approved_date)->format('d M Y') ?: '-' }}</td><td>{{ $tada->remark ?: '-' }}</td></tr>
                        @empty
                            <tr><td colspan="5">No TADA claims found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-pad">{{ $tadas->links() }}</div>
        </div>
    </div>
@endsection
