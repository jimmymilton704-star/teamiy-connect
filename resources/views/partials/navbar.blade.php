<header class="topbar">
    <button class="icon-btn" data-action="toggle-nav">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2" stroke-linecap="round">
            <path d="M3 6h18M3 12h18M3 18h18"></path>
        </svg>
    </button>

    <div class="grow">
        <h2 id="viewTitle">@yield('page_title', 'Dashboard')</h2>
    </div>

    <div class="topsearch">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
             stroke="rgba(255,255,255,.7)" stroke-width="2" stroke-linecap="round">
            <circle cx="11" cy="11" r="7"></circle>
            <path d="M21 21l-4-4"></path>
        </svg>
        <input placeholder="Search…">
    </div>

    <a class="top-icon" href="{{ url('/inbox') }}" id="topInbox">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="1.9"
             stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 7a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
            <path d="M3 7l9 6 9-6"></path>
        </svg>
    </a>

    <a class="top-icon" href="{{ url('/notices') }}" id="topNotices">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="1.9"
             stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 8.5a6 6 0 0 0-12 0c0 6-2.5 7.5-2.5 7.5h17S18 14.5 18 8.5"></path>
            <path d="M10 20a2 2 0 0 0 4 0"></path>
        </svg>
    </a>

    <button class="att-btn out" id="topAtt" data-action="att-toggle">
        <span class="pulse"></span>
        <span id="topAttLabel">Check In</span>
    </button>
</header>