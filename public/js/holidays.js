/* ===== Company Holidays page ===== */
(function () {
  'use strict';
  var TC = window.TC, esc = TC.esc, badge = TC.badge, I = TC.I;

  function view() {
    var state = TC.state;
    var monthAccent = { May: '#94A3B8', Jun: '#F47B26', Aug: '#057DB0', Sep: '#6B46C1', Nov: '#0E7C66', Dec: '#C0392B' };
    var rows = state.holidays.map(function (h) {
      return '<div class="row" style="gap:16px;padding:14px 18px;border-top:1px solid #F1F5F9"><div style="width:52px;text-align:center;flex:none"><div style="font-size:20px;font-weight:800;line-height:1;color:' + (h.past ? '#B6C0CD' : (monthAccent[h.month] || '#057DB0')) + '" class="tc-num">' + h.day + '</div><div style="font-size:11px;font-weight:700;color:#94A3B8;text-transform:uppercase">' + h.month + '</div></div><div style="width:1px;height:34px;background:#EEF2F7;flex:none"></div><div style="flex:1;min-width:0"><div style="font-size:14.5px;font-weight:700;color:#1E293B">' + esc(h.title) + '</div><div style="font-size:12.5px;color:#94A3B8">' + esc(h.weekday) + '</div></div>' + badge(h.type) + '</div>';
    }).join('');
    return '<div class="wrap-sm"><div class="holiday-card" style="padding:20px 22px;display:flex;align-items:center;gap:18px;margin-bottom:20px;flex-wrap:wrap"><div style="width:54px;height:54px;border-radius:14px;background:#F47B26;color:#fff;display:flex;align-items:center;justify-content:center;flex:none">' + I.holidays.replace(/width="18" height="18"/, 'width="26" height="26"') + '</div><div style="flex:1;min-width:0"><div style="font-size:11.5px;font-weight:800;color:#D2691E;letter-spacing:.05em">NEXT HOLIDAY · IN 4 DAYS</div><div style="font-size:20px;font-weight:800;color:#7A3D12;margin-top:3px">Eid al-Adha</div><div style="font-size:13px;color:#A85C2A;margin-top:2px">Friday 26 — Sunday 28 June 2026 · Public Holiday</div></div></div><div class="card"><div style="padding:16px 18px;font-size:15px;font-weight:800">Company Holidays · 2026</div>' + rows + '</div></div>';
  }

  TC.boot(view);
})();
