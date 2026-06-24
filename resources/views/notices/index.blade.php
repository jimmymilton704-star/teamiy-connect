@extends('layouts.app')

@section('title', 'Notices - Teamiy Connect')
@section('page', 'notices')
@section('page_title', 'Notices')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/notices.css') }}">
@endpush

@section('content')
    <div class="wrap">
        <section class="hero"><div class="blob"></div><div class="z"><div class="date">Notice board</div><div class="greet">Company Notices</div><div class="summary">Published company and branch announcements.</div></div></section>
        <div class="cards-grid auto-330" style="margin-top:18px">
            @forelse($notices as $notice)
                <article class="card card-pad hover-pop">
                    <div class="spread"><h3 class="section-title">{{ $notice->title }}</h3><span class="badge badge-blue">{{ optional($notice->notice_publish_date)->format('d M') }}</span></div>
                    <p style="font-size:13px;color:#64748B;line-height:1.55;margin-top:10px">{{ str($notice->description)->stripTags()->limit(220) }}</p>
                </article>
            @empty
                @include('partials.empty-state', ['message' => 'No notices found.'])
            @endforelse
        </div>
        <div style="margin-top:18px">{{ $notices->links() }}</div>
    </div>
@endsection
