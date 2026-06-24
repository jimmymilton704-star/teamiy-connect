@extends('layouts.app')

@section('title', 'Meetings - Teamiy Connect')
@section('page', 'meetings')
@section('page_title', 'Meetings')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/meetings.css') }}">
@endpush

@section('content')
    <div class="wrap">
        <section class="hero"><div class="blob"></div><div class="z"><div class="date">Team calendar</div><div class="greet">Meetings</div><div class="summary">Upcoming and recent team meetings.</div></div></section>
        <div class="cards-grid auto-330" style="margin-top:18px">
            @forelse($meetings as $meeting)
                <article class="card card-pad row">
                    <div class="meeting-date"><div class="d">{{ optional($meeting->meeting_date)->format('d') }}</div><div class="m">{{ optional($meeting->meeting_date)->format('M') }}</div></div>
                    <div style="min-width:0;flex:1">
                        <div style="font-weight:800;color:#1E293B">{{ $meeting->title }}</div>
                        <div style="font-size:13px;color:#64748B">{{ $meeting->venue }} · {{ $meeting->meeting_start_time }}</div>
                        @if($meeting->meeting_link)
                            <a class="btn btn-sm btn-primary" style="margin-top:10px" href="{{ $meeting->meeting_link }}" target="_blank">Join</a>
                        @endif
                    </div>
                </article>
            @empty
                @include('partials.empty-state', ['message' => 'No meetings found.'])
            @endforelse
        </div>
        <div style="margin-top:18px">{{ $meetings->links() }}</div>
    </div>
@endsection
