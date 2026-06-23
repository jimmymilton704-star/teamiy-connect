/* ===== Settings page ===== */
(function () {
  'use strict';
  var TC = window.TC;

  function toggle(label, on) { return '<label class="toggle-row"><span>' + label + '</span><input type="checkbox"' + (on ? ' checked' : '') + '></label>'; }
  function selField(label, opts) { return '<div><label class="label">' + label + '</label><select class="select">' + opts.map(function (o) { return '<option>' + o + '</option>'; }).join('') + '</select></div>'; }

  function view() {
    return '<div class="wrap-xs" style="display:flex;flex-direction:column;gap:18px">' +
      '<div class="card card-pad-lg"><div class="section-title" style="margin-bottom:4px">Notifications</div><div class="muted" style="font-size:12.5px;margin-bottom:8px">Pick what you want to be notified about.</div>' +
        toggle('Leave request updates', true) + toggle('TADA &amp; expense updates', true) + toggle('Meeting reminders', true) + toggle('New notices', true) + toggle('Weekly summary email', false) + '</div>' +
      '<div class="card card-pad-lg"><div class="section-title" style="margin-bottom:16px">Security</div>' +
        '<div class="spread" style="padding:11px 0;border-top:1px solid #F1F5F9"><div class="row" style="gap:10px"><span style="font-size:13.5px;font-weight:700;color:#1E293B">Two-factor authentication</span><span class="badge xs badge-amber">RECOMMENDED</span></div><input type="checkbox" style="width:18px;height:18px;accent-color:var(--primary)"></div>' +
        '<div class="spread" style="padding:11px 0;border-top:1px solid #F1F5F9"><div><div style="font-size:13.5px;font-weight:700;color:#1E293B">Password</div><div style="font-size:12px;color:#94A3B8;margin-top:1px">Last changed 2 months ago.</div></div><a class="btn btn-sm" style="background:#EAF5FB;color:var(--primary)" href="profile.html">Change password</a></div>' +
        '<div style="padding:14px 0 4px;border-top:1px solid #F1F5F9"><div style="font-size:12.5px;font-weight:800;color:#64748B;margin-bottom:10px">ACTIVE SESSIONS</div><div class="row" style="gap:12px;margin-bottom:12px"><div style="width:34px;height:34px;border-radius:9px;display:flex;align-items:center;justify-content:center;flex:none" class="tint-blue"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="3" y="4" width="18" height="13" rx="2"/><path d="M8 21h8M12 17v4"/></svg></div><div style="flex:1;min-width:0"><div style="font-size:13px;font-weight:700;color:#1E293B">MacBook Pro · Chrome <span class="badge xs badge-green" style="margin-left:4px">This device</span></div><div style="font-size:12px;color:#94A3B8">Karachi, PK · Active now</div></div></div><div class="row" style="gap:12px"><div style="width:34px;height:34px;border-radius:9px;display:flex;align-items:center;justify-content:center;flex:none" class="tint-violet"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="6" y="2.5" width="12" height="19" rx="2.5"/><path d="M10.5 18.5h3"/></svg></div><div style="flex:1;min-width:0"><div style="font-size:13px;font-weight:700;color:#1E293B">iPhone 15 · Teamiy App</div><div style="font-size:12px;color:#94A3B8">Karachi, PK · 3 hours ago</div></div><button class="btn btn-sm" style="background:transparent;color:#C0392B">Revoke</button></div></div></div>' +
      '<div class="card card-pad-lg"><div class="section-title" style="margin-bottom:16px">Language &amp; Region</div><div class="cards-grid auto-200" style="gap:14px">' +
        selField('Language', ['English', 'Italian', 'French', 'German', 'Spanish']) +
        selField('Time zone', ['(GMT+0) Western European Time — London, Lisbon, Dublin', '(GMT+1) Central European Time — Berlin, Paris, Rome, Madrid', '(GMT+2) Eastern European Time — Athens, Helsinki, Bucharest', '(GMT+3) Moscow Standard Time — Moscow, Istanbul']) +
        selField('Date format', ['DD MMM YYYY', 'MM/DD/YYYY', 'YYYY-MM-DD']) +
        '</div><button class="btn btn-primary" style="margin-top:18px" data-action="save-settings">Save preferences</button></div>' +
      '<div class="spread card" style="padding:16px 22px;border-color:#F2D9D9"><div><div style="font-size:13.5px;font-weight:700;color:#1E293B">Sign out</div><div style="font-size:12px;color:#94A3B8;margin-top:1px">End your session on this device.</div></div><button class="btn btn-danger btn-sm" data-action="logout">Sign out</button></div>' +
    '</div>';
  }

  Object.assign(TC.actions, {
    'save-settings': function () { TC.toast('Preferences saved'); }
  });

  TC.boot(view);
})();
