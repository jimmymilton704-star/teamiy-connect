/* ===== Assets page ===== */
(function () {
  'use strict';
  var TC = window.TC, esc = TC.esc, badge = TC.badge, I = TC.I;
  var assetIcons = TC.assetIcons, assetTint = TC.assetTint;

  function infoMini(k, v, num) { return '<div><div class="kicker">' + k + '</div><div style="font-size:12.5px;color:#475569;font-weight:600;margin-top:2px"' + (num ? ' class="tc-num"' : '') + '>' + esc(v) + '</div></div>'; }

  function view() {
    var state = TC.state;
    var cards = state.assets.map(function (a, i) {
      var bottom = '';
      if (a.status === 'Assigned') bottom = '<button class="btn btn-block" style="margin-top:16px;background:#FDEEE2;color:#C2691B;font-size:13px;padding:10px" data-action="return-asset" data-idx="' + i + '">' + I.ret + 'Return Asset</button>';
      else if (a.status === 'Return Pending') bottom = '<div class="btn btn-block" style="margin-top:16px;background:#FEF3E2;color:#B26A00;font-size:12.5px;padding:10px;cursor:default">' + I.clock.replace(/width="18" height="18"/, 'width="15" height="15"') + 'Awaiting admin approval…</div>';
      return '<div class="card card-pad"><div class="row" style="gap:13px"><div style="width:46px;height:46px;border-radius:12px;display:flex;align-items:center;justify-content:center;flex:none" class="' + (assetTint[a.kind] || 'tint-orange') + '">' + (assetIcons[a.kind] || assetIcons.card) + '</div><div style="min-width:0;flex:1"><div style="font-size:14.5px;font-weight:800;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">' + esc(a.name) + '</div><div style="font-size:12px;color:#94A3B8">' + esc(a.category) + '</div></div>' + badge(a.status) + '</div><div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:16px">' + infoMini('SERIAL NO.', a.serial, true) + infoMini('CONDITION', a.condition) + infoMini('ASSIGNED', a.assigned, true) + infoMini('BRAND', a.brand) + '</div>' + bottom + '</div>';
    }).join('');
    return '<div class="wrap"><div class="cards-grid auto-300">' + cards + '</div></div>';
  }

  Object.assign(TC.actions, {
    'return-asset': function (el, e, idx) {
      var i = +idx, state = TC.state;
      state.assets[i].status = 'Return Pending'; TC.toast('Return request sent — awaiting admin approval'); TC.render();
      setTimeout(function () { if (state.assets[i]) { state.assets[i].status = 'Returned'; TC.toast('Admin approved — asset returned'); TC.render(); } }, 3500);
    }
  });

  TC.boot(view);
})();
