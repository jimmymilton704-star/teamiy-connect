/* ===== TADA page ===== */
(function () {
  'use strict';
  var TC = window.TC, esc = TC.esc, badge = TC.badge, I = TC.I, fmtDate = TC.fmtDate;

  function tadaStat(l, v, c) { return '<div class="card" style="padding:16px"><div style="font-size:12.5px;color:#64748B;font-weight:700">' + l + '</div><div style="font-size:22px;font-weight:800;margin-top:6px;color:' + c + '" class="tc-num">' + v + '</div></div>'; }

  function view() {
    var state = TC.state;
    var rows = state.tadas.map(function (t) {
      return '<tr><td><div style="font-size:13.5px;font-weight:700;color:#1E293B">' + esc(t.type) + '</div><div style="font-size:11.5px;color:#94A3B8">' + esc(t.route) + '</div></td><td class="tc-num">' + esc(t.date) + '</td><td style="color:#64748B;max-width:200px">' + esc(t.purpose) + '</td><td class="tc-num" style="font-weight:700;color:#1E293B;text-align:right">' + esc(t.amount) + '</td><td>' + badge(t.status) + '</td><td style="color:#94A3B8;max-width:160px;font-size:12.5px">' + esc(t.remarks) + '</td></tr>';
    }).join('');
    return '<div class="wrap"><div class="cards-grid auto-150" style="margin-bottom:18px">' +
      tadaStat('Total Claimed', 'PKR 40,500', '#1E293B') + tadaStat('Approved', 'PKR 25,700', '#16A34A') + tadaStat('Pending', 'PKR 12,000', '#B26A00') + tadaStat('Paid', 'PKR 22,700', '#057DB0') +
      '</div><div class="card"><div class="spread" style="padding:16px 18px"><span class="section-title" style="margin-right:auto">TADA Claims</span><button class="btn btn-primary btn-sm" data-action="open-tada">' + I.plus + 'Create TADA</button></div><div class="table-wrap"><table class="table" style="min-width:780px"><thead><tr><th>TYPE</th><th>DATE</th><th>PURPOSE</th><th style="text-align:right">AMOUNT</th><th>STATUS</th><th>REMARKS</th></tr></thead><tbody>' + rows + '</tbody></table></div></div></div>';
  }

  TC.modals.tada = function () {
    var f = TC.state.tadaForm;
    return TC.ov('<div class="modal"><div class="modal-head"><h3>Create TADA Claim</h3><button class="modal-x" data-action="close-modal">' + I.x + '</button></div><div class="modal-body"><div class="grid-2"><div><label class="label">Type</label><select class="select" data-model="tadaForm.type">' + ['Travel', 'Daily Allowance', 'Food', 'Hotel', 'Fuel', 'Other'].map(function (o) { return '<option' + (o === f.type ? ' selected' : '') + '>' + o + '</option>'; }).join('') + '</select></div><div><label class="label">Claim date</label><input type="date" class="input" data-model="tadaForm.date" value="' + f.date + '"></div><div><label class="label">From</label><input class="input" data-model="tadaForm.from" placeholder="Origin" value="' + esc(f.from) + '"></div><div><label class="label">To</label><input class="input" data-model="tadaForm.to" placeholder="Destination" value="' + esc(f.to) + '"></div></div><label class="label" style="margin-top:14px">Amount (PKR)</label><input class="input" type="number" data-model="tadaForm.amount" placeholder="0" value="' + esc(f.amount) + '"><label class="label" style="margin-top:14px">Purpose</label><textarea class="textarea" data-model="tadaForm.purpose" placeholder="Reason for claim…" rows="2">' + esc(f.purpose) + '</textarea><div class="dashed">' + I.upload + 'Attach receipt (optional)</div></div><div class="modal-foot"><button class="btn btn-ghost" data-action="close-modal">Cancel</button><button class="btn btn-primary" data-action="submit-tada">Submit claim</button></div></div>');
  };

  function submitTada() {
    var state = TC.state, f = state.tadaForm, amt = parseFloat(f.amount);
    if (!amt || amt <= 0) return TC.toast('Enter a valid amount');
    if (!f.purpose.trim()) return TC.toast('Please enter a purpose');
    var route = f.from && f.to ? f.from + ' → ' + f.to : (f.from || f.to || '—');
    state.tadas.unshift({ type: f.type, route: route, date: fmtDate(f.date), purpose: f.purpose, amount: 'PKR ' + amt.toLocaleString(), status: 'Pending', remarks: '—' });
    f.from = ''; f.to = ''; f.purpose = ''; f.amount = ''; state.modal = null; TC.toast('TADA claim submitted'); TC.render();
  }

  Object.assign(TC.actions, {
    'open-tada': function () { TC.openModal('tada'); },
    'submit-tada': submitTada
  });

  TC.boot(view);
})();
