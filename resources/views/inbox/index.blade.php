@extends('layouts.app')

@section('title', 'Inbox - Teamiy Connect')
@section('page', 'inbox')
@section('page_title', 'Inbox')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/inbox.css') }}">
@endpush

@section('content')
    <div class="wrap">
        <section class="hero"><div class="blob"></div><div class="z"><div class="date">Notifications</div><div class="greet">Inbox</div><div class="summary">Direct and company-wide notifications.</div></div></section>
        <div class="cards-grid auto-330" style="margin-top:18px">
            @forelse($userNotifications as $item)
                <article class="card card-pad">
                    <div class="spread">
                        <h3 class="section-title">{{ $item->notification->title ?? 'Notification' }}</h3>
                        <span class="badge {{ $item->is_seen ? 'badge-gray' : 'badge-blue' }}">{{ $item->is_seen ? 'Seen' : 'New' }}</span>
                    </div>
                    <p style="font-size:13px;color:#64748B;line-height:1.55;margin-top:10px">{{ str($item->notification->description ?? '')->stripTags()->limit(180) }}</p>
                    <div style="font-size:12px;color:#94A3B8;margin-top:8px">{{ optional($item->created_at)->format('d M Y h:i A') }}</div>
                </article>
            @empty
                @include('partials.empty-state', ['message' => 'No direct notifications found.'])
            @endforelse
        </div>
        <div style="margin-top:18px">{{ $userNotifications->links() }}</div>

        <div class="card" style="margin-top:18px">
            <div class="card-pad-lg"><h3 class="section-title">Company Broadcasts</h3></div>
            @foreach($generalNotifications as $notification)
                <div class="list-row">
                    <span class="dot on"></span>
                    <div><div style="font-weight:800;color:#1E293B">{{ $notification->title }}</div><div style="font-size:13px;color:#64748B">{{ str($notification->description)->stripTags()->limit(160) }}</div></div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
