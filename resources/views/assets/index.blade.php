@extends('layouts.app')

@section('title', 'Assets - Teamiy Connect')
@section('page', 'assets')
@section('page_title', 'Assets')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/assets.css') }}">
@endpush

@section('content')
    <div class="wrap">
        <section class="hero"><div class="blob"></div><div class="z"><div class="date">Assigned equipment</div><div class="greet">My Assets</div><div class="summary">Assets and documents assigned to your employee record.</div></div></section>
        <div class="card" style="margin-top:18px">
            <div class="table-wrap">
                <table class="table">
                    <thead><tr><th>Asset</th><th>Type</th><th>Code</th><th>Assigned</th><th>Status</th><th>Condition</th></tr></thead>
                    <tbody>
                        @forelse($assignments as $assignment)
                            <tr><td>{{ $assignment->asset->name ?? '-' }}</td><td>{{ $assignment->asset->type->name ?? '-' }}</td><td>{{ $assignment->asset->asset_code ?? '-' }}</td><td>{{ optional($assignment->assigned_date)->format('d M Y') ?: '-' }}</td><td>@include('partials.status-badge', ['slot' => $assignment->status])</td><td>{{ $assignment->return_condition ?: '-' }}</td></tr>
                        @empty
                            <tr><td colspan="6">No assigned assets found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-pad">{{ $assignments->links() }}</div>
        </div>
        <div class="cards-grid auto-250" style="margin-top:18px">
            @foreach($documents as $document)
                <div class="card card-pad"><div class="section-title">Employee Documents</div><div style="font-size:13px;color:#64748B;margin-top:8px">Contract: {{ $document->employee_contract ?: '-' }}</div><div style="font-size:13px;color:#64748B;margin-top:4px">Document: {{ $document->employee_document ?: '-' }}</div></div>
            @endforeach
        </div>
    </div>
@endsection
