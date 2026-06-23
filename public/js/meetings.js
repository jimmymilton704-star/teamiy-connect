/* ===== Team Meetings page ===== */
(function () {
  'use strict';
  var TC = window.TC, esc = TC.esc, badge = TC.badge, I = TC.I;

  function platCls(p) { return p === 'Zoom' ? 'badge-blue' : p === 'Google Meet' ? 'badge-green' : 'badge-violet'; }

  function view() {
    var state = TC.state;
    var up = state.meetings.filter(function (m) { return m.when === 'upcoming'; }).map(function (m) {
      var i = state.meetings.indexOf(m);
      var hl = m.day === '22';
      return '<div class="card card-pad-lg row flex-wrap" style="gap:16px"><div class="meeting-date" style="' + (hl ? 'background:#EAF3FC' : '') + '"><div class="d" style="color:' + (hl ? '#057DB0' : '#64748B') + '">' + m.day + '</div><div class="m" style="color:' + (hl ? '#057DB0' : '#64748B') + '">' + m.month + '</div></div><div style="flex:1;min-width:160px"><div style="font-size:15px;font-weight:800;color:#1E293B">' + esc(m.title) + '</div><div class="row flex-wrap" style="gap:14px;margin-top:5px;font-size:12.5px;color:#94A3B8;font-weight:600"><span class="tc-num">🕐 ' + m.time + '</span><span>' + esc(m.host) + '</span><span class="badge xs ' + platCls(m.platform) + '">' + m.platform + '</span></div></div><button class="btn" style="background:#16A34A;color:#fff" data-action="join-meeting" data-idx="' + i + '">' + I.meetings.replace(/width="18" height="18"/, 'width="15" height="15"') + 'Join</button></div>';
    }).join('');
    var past = state.meetings.filter(function (m) { return m.when === 'past'; }).map(function (m) {
      return '<div class="card card-pad-lg row" style="gap:16px;opacity:.78"><div class="meeting-date"><div class="d" style="color:#94A3B8">' + m.day + '</div><div class="m" style="color:#94A3B8">' + m.month + '</div></div><div style="flex:1;min-width:0"><div style="font-size:15px;font-weight:700;color:#475569">' + esc(m.title) + '</div><div style="font-size:12.5px;color:#94A3B8;margin-top:4px;font-weight:600" class="tc-num">' + m.time + ' · ' + esc(m.host) + '</div></div>' + badge(m.status) + '</div>';
    }).join('');
    return '<div class="wrap-sm"><div style="font-size:13px;font-weight:800;color:#64748B;letter-spacing:.03em;margin-bottom:12px">UPCOMING</div><div style="display:flex;flex-direction:column;gap:12px;margin-bottom:26px">' + up + '</div><div style="font-size:13px;font-weight:800;color:#64748B;letter-spacing:.03em;margin-bottom:12px">PAST</div><div style="display:flex;flex-direction:column;gap:12px">' + past + '</div></div>';
  }

  Object.assign(TC.actions, {
    'join-meeting': function (el, e, idx) { var mk = TC.state.meetings[+idx]; if (mk.link) window.open(mk.link, '_blank'); }
  });

  TC.boot(view);
})();
