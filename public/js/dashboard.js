/* ===== Dashboard page ===== */
(function () {
  'use strict';
  var TC = window.TC, esc = TC.esc, badge = TC.badge, I = TC.I;

  function view() {
    var state = TC.state;
    var vm = TC.attVM();
    var notices = state.notices.slice(0, 4).map(function (n) {
      return '<div class="list-row" data-action="nav" data-view="notices"><span class="dot ' + (n.read ? 'off' : 'on') + '"></span><div style="flex:1;min-width:0"><div style="font-size:13.5px;font-weight:' + (n.read ? 600 : 800) + ';color:#1E293B;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">' + esc(n.title) + '</div><div style="font-size:12px;color:#94A3B8;margin-top:2px">' + esc(n.category) + ' · ' + esc(n.date) + '</div></div>' + badge(n.priority) + '</div>';
    }).join('');
    return '<div class="wrap">' +
      '<div class="hero"><div class="blob"></div>' +
        '<div class="z"><div class="date">' + TC.TODAY_LONG + '</div><div class="greet">Good morning, Ayesha 👋</div><div class="summary">' + esc(vm.summary) + '</div></div>' +
        '<div class="hero-actions"><button class="hero-btn" data-action="att-toggle">' + esc(vm.heroLabel) + '</button><a class="hero-btn ghost" href="leave.html?new=1">Request leave</a></div>' +
      '</div>' +
      '<div class="cards-grid auto-250" style="margin-top:18px">' +
        '<div class="card card-pad"><div class="spread"><span class="lbl">Today\'s Attendance</span><span class="ico tint-blue">' + I.clock.replace(/width="18" height="18"/, 'width="16" height="16"') + '</span></div><div style="margin-top:14px;display:flex;align-items:baseline;gap:8px"><span style="font-size:22px;font-weight:800" class="tc-num">' + esc(vm.cardValue) + '</span>' + badge(vm.statusLabel, 'xs') + '</div><div style="font-size:12.5px;color:#94A3B8;margin-top:8px">' + esc(vm.cardSub) + '</div><button class="btn ' + (vm.btnClass === 'done' ? 'btn-ghost' : 'btn-primary') + ' btn-block" style="margin-top:14px" data-action="att-toggle">' + esc(vm.btnLabel) + '</button></div>' +
        '<div class="card card-pad clickable" data-action="nav" data-view="leave"><div class="spread"><span class="lbl">Leave Balance</span><span class="ico tint-violet">' + I.leave.replace(/width="18" height="18"/, 'width="16" height="16"') + '</span></div><div style="margin-top:14px;display:flex;align-items:baseline;gap:6px"><span style="font-size:28px;font-weight:800" class="tc-num">22</span><span style="font-size:13px;color:#94A3B8;font-weight:600">days available</span></div><div style="font-size:12.5px;color:#94A3B8;margin-top:8px"><strong style="color:#B26A00">' + TC.pendingLeaveCount() + ' pending</strong> · Annual 12 · Sick 6 · Casual 4</div></div>' +
        '<div class="card card-pad clickable" data-action="nav" data-view="projects"><div class="spread"><span class="lbl">Active Projects</span><span class="ico tint-blue">' + I.projects.replace(/width="18" height="18"/, 'width="16" height="16"') + '</span></div><div style="margin-top:14px;display:flex;align-items:baseline;gap:6px"><span style="font-size:28px;font-weight:800" class="tc-num">3</span><span style="font-size:13px;color:#94A3B8;font-weight:600">in progress</span></div><div style="font-size:12.5px;color:#94A3B8;margin-top:8px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">Teamiy Mobile App · Customer Portal · DS 2.0</div></div>' +
        '<div class="card card-pad clickable" data-action="nav" data-view="assets"><div class="spread"><span class="lbl">Assigned Assets</span><span class="ico tint-orange">' + I.assets.replace(/width="18" height="18"/, 'width="16" height="16"') + '</span></div><div style="margin-top:14px;display:flex;align-items:baseline;gap:6px"><span style="font-size:28px;font-weight:800" class="tc-num">4</span><span style="font-size:13px;color:#94A3B8;font-weight:600">items</span></div><div style="font-size:12.5px;color:#94A3B8;margin-top:8px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">MacBook Pro · iPhone 15 · Headset · Access Card</div></div>' +
      '</div>' +
      '<div style="display:grid;grid-template-columns:1.4fr 1fr;gap:16px;margin-top:16px">' +
        '<div class="card"><div class="spread" style="padding:16px 18px 12px"><span class="section-title">Latest Notices</span><a class="link" style="font-size:12.5px" href="notices.html">View all</a></div>' + notices + '</div>' +
        '<div style="display:flex;flex-direction:column;gap:16px">' +
          '<div class="card card-pad-lg"><div class="spread" style="margin-bottom:10px"><span class="section-title">Next Meeting</span><a class="link" style="font-size:12.5px" href="meetings.html">All</a></div><div style="font-size:14px;font-weight:700;color:#1E293B">Sprint Planning — Mobile App</div><div style="font-size:12.5px;color:#94A3B8;margin-top:4px">Today · 3:00 PM · Hamza Sheikh</div><a class="btn btn-block" style="margin-top:12px;background:#16A34A;color:#fff" href="meetings.html">' + I.meetings.replace(/width="18" height="18"/, 'width="15" height="15"') + 'Join meeting</a></div>' +
          '<div class="holiday-card"><div class="holiday-kicker">' + I.holidays.replace(/width="18" height="18"/, 'width="15" height="15"') + 'Upcoming Holiday</div><div style="font-size:17px;font-weight:800;color:#7A3D12;margin-top:8px">Eid al-Adha</div><div style="font-size:13px;color:#A85C2A;margin-top:2px">Fri 26 — Sun 28 June · Public Holiday</div><div style="font-size:12.5px;color:#C07B45;margin-top:8px;font-weight:600">In 4 days · 3-day weekend</div></div>' +
        '</div>' +
      '</div>' +
    '</div>';
  }

  TC.boot(view);
})();
