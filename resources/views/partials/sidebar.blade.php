@php
    $user = auth()->user();

    $name = $user->name ?? 'Ayesha Khan';
    $role = $user->designation ?? $user->role ?? 'Sr. Frontend Engineer';

    $initials = collect(explode(' ', $name))
        ->filter()
        ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
        ->take(2)
        ->implode('');

    $pendingLeaves = $pendingLeaves ?? 0;
    $unreadNotices = $unreadNotices ?? 0;
    $attendance = attendance_rules();
@endphp

<aside class="sidebar">
    <div class="sidebar-head">
        <a href="{{ route('dashboard') }}">
            <img src="{{ asset('assets/logo.png') }}" alt="Teamiy">
        </a>
    </div>

    <nav class="nav" id="nav">
        <div class="nav-section">OVERVIEW</div>

        <a class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="7" height="7" rx="1.5"/>
                <rect x="14" y="3" width="7" height="7" rx="1.5"/>
                <rect x="14" y="14" width="7" height="7" rx="1.5"/>
                <rect x="3" y="14" width="7" height="7" rx="1.5"/>
            </svg>
            <span>Dashboard</span>
        </a>

        <div class="nav-section mt">MY WORK</div>

        <a class="nav-item {{ request()->is('leave*') ? 'active' : '' }}" href="{{ url('/leave') }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4.5" width="18" height="16" rx="2.5"/>
                <path d="M3 9h18M8 2.5v4M16 2.5v4"/>
            </svg>
            <span>Leave</span>

            @if($pendingLeaves > 0)
                <em class="nav-badge">{{ $pendingLeaves }}</em>
            @endif
        </a>

        <a class="nav-item {{ request()->is('attendance*') ? 'active' : '' }}" href="{{ url('/attendance') }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="9"/>
                <path d="M12 7.5V12l3 2"/>
            </svg>
            <span>Attendance</span>
        </a>

        <a class="nav-item {{ request()->is('tada*') ? 'active' : '' }}" href="{{ url('/tada') }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 3.5h14v17l-2.5-1.5L14 20.5 12 19l-2 1.5L7.5 19 5 20.5z"/>
                <path d="M9 8h6M9 12h6"/>
            </svg>
            <span>TADA</span>
        </a>

        <div class="nav-section mt">COMPANY</div>

        <a class="nav-item {{ request()->is('team-sheet*') ? 'active' : '' }}" href="{{ url('/team-sheet') }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="9" cy="8" r="3.2"/>
                <path d="M3.5 19c0-3 2.5-4.6 5.5-4.6s5.5 1.6 5.5 4.6"/>
                <path d="M16 5.2a3.2 3.2 0 0 1 0 6.1M17.5 14.6c2.2.5 3.5 1.9 3.5 4.4"/>
            </svg>
            <span>Team Sheet</span>
        </a>

        <a class="nav-item {{ request()->is('projects*') ? 'active' : '' }}" href="{{ url('/projects') }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 7a2 2 0 0 1 2-2h4l2 2.5h8a2 2 0 0 1 2 2V18a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
            </svg>
            <span>Projects</span>
        </a>

        <a class="nav-item {{ request()->is('assets*') ? 'active' : '' }}" href="{{ url('/assets') }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2.5" y="4.5" width="19" height="12" rx="2"/>
                <path d="M2 20.5h20M9.5 20.5l.5-4M14.5 20.5l-.5-4"/>
            </svg>
            <span>Assets</span>
        </a>

        <a class="nav-item {{ request()->is('holidays*') ? 'active' : '' }}" href="{{ url('/holidays') }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="4"/>
                <path d="M12 2v2.5M12 19.5V22M2 12h2.5M19.5 12H22M4.8 4.8l1.8 1.8M17.4 17.4l1.8 1.8M19.2 4.8l-1.8 1.8M6.6 17.4l-1.8 1.8"/>
            </svg>
            <span>Holidays</span>
        </a>

        <a class="nav-item {{ request()->is('notices*') ? 'active' : '' }}" href="{{ url('/notices') }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 8.5a6 6 0 0 0-12 0c0 6-2.5 7.5-2.5 7.5h17S18 14.5 18 8.5"/>
                <path d="M10 20a2 2 0 0 0 4 0"/>
            </svg>
            <span>Notices</span>

            @if($unreadNotices > 0)
                <em class="nav-badge red">{{ $unreadNotices }}</em>
            @endif
        </a>

        <a class="nav-item {{ request()->is('meetings*') ? 'active' : '' }}" href="{{ url('/meetings') }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2.5" y="6" width="13" height="12" rx="2.5"/>
                <path d="M15.5 10l6-3v10l-6-3z"/>
            </svg>
            <span>Meetings</span>
        </a>

        <div class="nav-divider"></div>

        <a class="nav-item {{ request()->is('profile*') ? 'active' : '' }}" href="{{ url('/profile') }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="3"/>
                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 8 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H2a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 3.6 8a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33h.09a1.65 1.65 0 0 0 1-1.51V2a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82v.09a1.65 1.65 0 0 0 1.51 1H22a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
            </svg>
            <span>Settings</span>
        </a>
    </nav>

    <div class="sidebar-foot">
        <div class="user-chip">
            <a class="avatar av-c0" href="{{ url('/profile') }}"
               style="width:34px;height:34px;font-size:13px;text-decoration:none">
                {{ $initials ?: 'AK' }}
            </a>

            <a class="meta" href="{{ url('/profile') }}" style="text-decoration:none">
                <div class="nm">{{ $name }}</div>
                <div class="rl">{{ $role }}</div>
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="icon-btn" type="submit" title="Sign out">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.9"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"></path>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</aside>