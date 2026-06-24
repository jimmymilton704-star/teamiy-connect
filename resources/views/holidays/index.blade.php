@extends('layouts.app')

@section('title', 'Holidays - Teamiy Connect')
@section('page', 'holidays')
@section('page_title', 'Holidays')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/holidays.css') }}">
@endpush

@section('content')
    <div class="wrap">
        <section class="hero"><div class="blob"></div><div class="z"><div class="date">Company calendar</div><div class="greet">Holidays</div><div class="summary">{{ $nextHoliday ? 'Next: '.$nextHoliday->event.' on '.$nextHoliday->event_date->format('d M Y') : 'No upcoming holiday found.' }}</div></div></section>
        <div class="cards-grid auto-250" style="margin-top:18px">
            @forelse($holidays as $holiday)
                <div class="holiday-card">
                    <div class="holiday-kicker">{{ $holiday->is_public_holiday ? 'Public Holiday' : 'Company Holiday' }}</div>
                    <div style="font-size:18px;font-weight:800;color:#7A3D12;margin-top:8px">{{ $holiday->event }}</div>
                    <div style="font-size:13px;color:#A85C2A;margin-top:2px">{{ optional($holiday->event_date)->format('l, d M Y') }}</div>
                    <div style="font-size:12.5px;color:#C07B45;margin-top:8px">{{ $holiday->note }}</div>
                </div>
            @empty
                @include('partials.empty-state', ['message' => 'No holidays found.'])
            @endforelse
        </div>
        <div style="margin-top:18px">{{ $holidays->links() }}</div>
    </div>
@endsection
