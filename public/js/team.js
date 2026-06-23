/* ===== Team Sheet page ===== */
(function () {
  'use strict';
  var TC = window.TC, esc = TC.esc, badge = TC.badge, avatar = TC.avatar, initials = TC.initials, colorClass = TC.colorClass, TEAM = TC.TEAM;

  function view() {
    var state = TC.state;
    var q = state.teamQuery.trim().toLowerCase();
    var list = TEAM.filter(function (m) { return !q || m.name.toLowerCase().includes(q) || m.dept.toLowerCase().includes(q) || m.role.toLowerCase().includes(q); });
    var cards = list.map(function (m) {
      var i = TEAM.indexOf(m);
      return '<div class="card card-pad clickable hover-pop" data-action="open-member" data-idx="' + i + '"><div class="row" style="gap:12px">' + avatar(m.name, 46, true) + '<div style="min-width:0;flex:1"><div style="font-size:14.5px;font-weight:800;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">' + esc(m.name) + '</div><div style="font-size:12.5px;color:#64748B;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">' + esc(m.role) + '</div></div></div><div class="row flex-wrap" style="gap:8px;margin-top:14px"><span style="font-size:11.5px;font-weight:700;color:#475569;background:#F1F5F9;padding:4px 9px;border-radius:7px">' + esc(m.dept) + '</span>' + badge(m.status) + '</div><div class="row" style="gap:7px;margin-top:12px;font-size:12px;color:#94A3B8"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg><span style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis">' + esc(m.email) + '</span></div></div>';
    }).join('');
    return '<div class="wrap"><div class="row flex-wrap" style="gap:12px;margin-bottom:18px"><div class="topsearch" style="background:#fff;border:1.5px solid #E7ECF3;width:300px"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94A3B8" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4-4"/></svg><input id="teamSearch" placeholder="Search by name, department…" value="' + esc(state.teamQuery) + '" style="color:#334155"></div><span style="font-size:13px;color:#94A3B8;font-weight:600">' + list.length + ' members</span></div><div class="cards-grid auto-258">' + cards + '</div></div>';
  }

  function memberRow(k, v, num) { return '<div class="spread" style="background:#fff;padding:12px 16px"><span style="font-size:12.5px;color:#94A3B8;font-weight:700">' + k + '</span><span style="font-size:12.5px;color:#334155;font-weight:600"' + (num ? ' class="tc-num"' : '') + '>' + esc(v) + '</span></div>'; }

  TC.modals.member = function () {
    var m = TEAM[TC.state.activeMember];
    return TC.ov('<div class="modal" style="max-width:440px;overflow:hidden"><div style="height:74px;background:linear-gradient(120deg,#044d6e,#057DB0 60%,#16A6DE);position:relative"><button class="modal-x" style="position:absolute;top:14px;right:14px;color:#fff;opacity:.85" data-action="close-modal">' + I_x() + '</button></div><div style="padding:0 24px 24px;margin-top:-34px;text-align:center"><div style="margin:0 auto;width:72px" class="' + colorClass(m.name) + '"><div class="avatar ' + colorClass(m.name) + '" style="width:72px;height:72px;border-radius:20px;font-size:26px;border:4px solid #fff;box-shadow:0 6px 16px -8px rgba(0,0,0,.3)">' + initials(m.name) + '</div></div><div style="font-size:19px;font-weight:800;margin-top:12px">' + esc(m.name) + '</div><div style="font-size:13.5px;color:#64748B">' + esc(m.role) + '</div><div class="row" style="justify-content:center;gap:8px;margin-top:10px"><span style="font-size:11.5px;font-weight:700;color:#475569;background:#F1F5F9;padding:4px 10px;border-radius:7px">' + esc(m.dept) + '</span>' + badge(m.status) + '</div><div style="text-align:left;margin-top:20px;display:grid;gap:1px;background:var(--line-2);border:1px solid var(--line-2);border-radius:12px;overflow:hidden">' + memberRow('Email', m.email) + memberRow('Phone', m.phone, true) + memberRow('Reports to', m.manager) + memberRow('Joined', m.joined, true) + '</div></div></div>');
  };
  function I_x() { return TC.I.x.replace('width="22" height="22"', 'width="20" height="20"'); }

  Object.assign(TC.actions, {
    'open-member': function (el, e, idx) { TC.state.activeMember = +idx; TC.openModal('member'); }
  });

  // live team search (debounced re-render, keeps caret)
  TC.inputHooks.push(function (e) {
    if (e.target.id !== 'teamSearch') return;
    TC.state.teamQuery = e.target.value;
    clearTimeout(window.__ts);
    window.__ts = setTimeout(function () {
      var v = e.target.value; TC.render();
      var nf = document.getElementById('teamSearch');
      if (nf) { nf.focus(); nf.setSelectionRange(v.length, v.length); }
    }, 250);
  });
  TC.keydownHooks.push(function (e) { if (e.target.id === 'teamSearch' && e.key === 'Enter') TC.render(); });

  TC.boot(view);
})();
