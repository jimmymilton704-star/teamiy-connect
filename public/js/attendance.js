/* ===== Attendance page ===== */
(function () {
  'use strict';
  var TC = window.TC, esc = TC.esc, badge = TC.badge;

  function view() {
    var vm = TC.attVM();
    var hist = TC.attHistory().map(function (a) {
      return '<tr><td><div style="font-size:13.5px;font-weight:700;color:#1E293B" class="tc-num">' + a.date + '</div><div style="font-size:11.5px;color:#94A3B8">' + a.day + '</div></td><td class="tc-num">' + a.in + '</td><td class="tc-num">' + a.out + '</td><td class="tc-num" style="font-weight:600">' + a.hours + '</td><td>' + badge(a.status) + '</td></tr>';
    }).join('');
    return '<div class="wrap">' +
      '<div style="display:grid;grid-template-columns:1.2fr 2fr;gap:16px;margin-bottom:18px">' +
        '<div class="hero" style="border-radius:18px;padding:22px;display:block"><div class="blob" style="width:180px;height:180px;top:-70px;right:-30px"></div><div class="z"><div class="date">' + TC.TODAY_LONG + '</div><div style="display:flex;align-items:baseline;gap:10px;margin-top:12px"><span style="font-size:32px;font-weight:800" class="tc-num">' + esc(vm.cardValue) + '</span><span class="badge xs" style="background:rgba(255,255,255,.22);color:#fff">' + esc(vm.statusLabel) + '</span></div><div style="font-size:13px;color:rgba(255,255,255,.82);margin-top:8px">' + esc(vm.cardSub) + '</div><button class="hero-btn" style="margin-top:16px" data-action="att-toggle">' + esc(vm.btnLabel) + '</button></div></div>' +
        '<div class="cols-3">' +
          '<div class="card" style="padding:16px"><div style="font-size:12px;color:#64748B;font-weight:700">This Month</div><div style="font-size:24px;font-weight:800;margin-top:8px" class="tc-num">18</div><div style="font-size:11.5px;color:#94A3B8;margin-top:2px">days present</div></div>' +
          '<div class="card" style="padding:16px"><div style="font-size:12px;color:#64748B;font-weight:700">Late</div><div style="font-size:24px;font-weight:800;margin-top:8px;color:#B26A00" class="tc-num">2</div><div style="font-size:11.5px;color:#94A3B8;margin-top:2px">arrivals</div></div>' +
          '<div class="card" style="padding:16px"><div style="font-size:12px;color:#64748B;font-weight:700">Avg Hours</div><div style="font-size:24px;font-weight:800;margin-top:8px" class="tc-num">8h 52m</div><div style="font-size:11.5px;color:#94A3B8;margin-top:2px">per day</div></div>' +
        '</div>' +
      '</div>' +
      '<div class="card"><div class="spread" style="padding:16px 18px"><span class="section-title" style="margin-right:auto">Attendance History</span><span style="font-size:12.5px;color:#94A3B8;font-weight:600">June 2026</span></div><div class="table-wrap"><table class="table" style="min-width:680px"><thead><tr><th>DATE</th><th>CHECK IN</th><th>CHECK OUT</th><th>HOURS</th><th>STATUS</th></tr></thead><tbody>' + hist + '</tbody></table></div></div>' +
    '</div>';
  }

  TC.boot(view);
})();
