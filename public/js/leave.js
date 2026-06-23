/* ===== Leave Management page ===== */
(function () {
  'use strict';
  var TC = window.TC, esc = TC.esc, badge = TC.badge, I = TC.I, fmtDate = TC.fmtDate, t12 = TC.t12;

  function balanceCard(label, val, total, cls) {
    var pct = Math.round(val / total * 100);
    return '<div class="card" style="padding:16px"><div style="font-size:12.5px;color:#64748B;font-weight:700">' + label + '</div><div style="display:flex;align-items:baseline;gap:5px;margin-top:6px"><span style="font-size:24px;font-weight:800" class="tc-num">' + val + '</span><span style="font-size:12px;color:#94A3B8">/ ' + total + ' left</span></div><div class="progress" style="margin-top:8px"><div class="progress-bar ' + cls + '" style="width:' + pct + '%"></div></div></div>';
  }

  function view() {
    var state = TC.state;
    var rows = state.leaves.filter(function (r) { return state.leaveFilter === 'All' || r.status === state.leaveFilter; }).map(function (r) {
      return '<tr><td><div style="font-size:13.5px;font-weight:700;color:#1E293B">' + esc(r.type) + '</div><div style="font-size:11.5px;color:#94A3B8">' + esc(r.mode) + '</div></td><td class="tc-num">' + esc(r.dates) + '</td><td class="tc-num" style="font-weight:600">' + esc(r.duration) + '</td><td style="color:#64748B;max-width:200px">' + esc(r.reason) + '</td><td>' + badge(r.status) + '</td><td style="color:#94A3B8;max-width:170px;font-size:12.5px">' + esc(r.remarks) + '</td></tr>';
    }).join('');
    var opts = ['All', 'Pending', 'Approved', 'Rejected', 'Cancelled'].map(function (o) { return '<option' + (o === state.leaveFilter ? ' selected' : '') + '>' + o + '</option>'; }).join('');
    return '<div class="wrap">' +
      '<div class="cards-grid auto-160" style="margin-bottom:20px">' +
        balanceCard('Annual', 12, 18, 'bar-blue') + balanceCard('Sick', 6, 10, 'bar-green') + balanceCard('Casual', 4, 8, 'bar-amber') +
        '<div class="card" style="padding:16px"><div style="font-size:12.5px;color:#64748B;font-weight:700">Pending</div><div style="display:flex;align-items:baseline;gap:5px;margin-top:6px"><span style="font-size:24px;font-weight:800;color:#B26A00" class="tc-num">' + TC.pendingLeaveCount() + '</span><span style="font-size:12px;color:#94A3B8">requests</span></div><div style="font-size:11.5px;color:#94A3B8;margin-top:10px">Awaiting approval</div></div>' +
      '</div>' +
      '<div class="card">' +
        '<div class="spread flex-wrap" style="padding:16px 18px"><span class="section-title" style="margin-right:auto">Leave History</span><select class="select" data-model="filter-leave" style="width:auto">' + opts + '</select><button class="btn btn-primary btn-sm" data-action="open-leave">' + I.plus + 'Request Leave</button></div>' +
        '<div class="table-wrap"><table class="table" style="min-width:760px"><thead><tr><th>TYPE</th><th>DATES</th><th>DURATION</th><th>REASON</th><th>STATUS</th><th>REMARKS</th></tr></thead><tbody>' + rows + '</tbody></table></div>' +
      '</div>' +
    '</div>';
  }

  TC.modals.leave = function () {
    var f = TC.state.leaveForm;
    var typeOpts = ['Annual Leave', 'Sick Leave', 'Casual Leave', 'Short Leave'].map(function (o) { return '<option' + (o === f.type ? ' selected' : '') + '>' + o + '</option>'; }).join('');
    var modeOpts = ['Full Day', 'Half Day', 'Multi Day', 'Short Leave'].map(function (o) { return '<option' + (o === f.mode ? ' selected' : '') + '>' + o + '</option>'; }).join('');
    var second = '';
    if (f.mode === 'Multi Day') second = '<div><label class="label">End date</label><input type="date" class="input" data-model="leaveForm.end" value="' + f.end + '"></div>';
    else if (f.mode === 'Half Day') second = '<div><label class="label">Half</label><select class="select" data-model="leaveForm.half">' + ['First Half', 'Second Half'].map(function (o) { return '<option' + (o === f.half ? ' selected' : '') + '>' + o + '</option>'; }).join('') + '</select></div>';
    else if (f.mode === 'Short Leave') second = '<div><label class="label">From time</label><input type="time" class="input" data-model="leaveForm.fromTime" value="' + f.fromTime + '"></div><div><label class="label">To time</label><input type="time" class="input" data-model="leaveForm.toTime" value="' + f.toTime + '"></div>';
    return TC.ov('<div class="modal"><div class="modal-head"><h3>Request Leave</h3><button class="modal-x" data-action="close-modal">' + I.x + '</button></div><div class="modal-body"><div class="grid-2"><div><label class="label">Leave type</label><select class="select" data-model="leaveForm.type">' + typeOpts + '</select></div><div><label class="label">Mode</label><select class="select" data-model="leaveForm.mode" data-rerender="1">' + modeOpts + '</select></div><div><label class="label">' + (f.mode === 'Multi Day' ? 'Start date' : 'Date') + '</label><input type="date" class="input" data-model="leaveForm.start" value="' + f.start + '"></div>' + second + '</div><label class="label" style="margin-top:14px">Reason</label><textarea class="textarea" data-model="leaveForm.reason" placeholder="Briefly describe your reason…" rows="3">' + esc(f.reason) + '</textarea><div class="dashed">' + I.upload + 'Attach document (optional)</div></div><div class="modal-foot"><button class="btn btn-ghost" data-action="close-modal">Cancel</button><button class="btn btn-primary" data-action="submit-leave">Submit request</button></div></div>');
  };

  function submitLeave() {
    var state = TC.state, f = state.leaveForm;
    if (!f.reason.trim()) return TC.toast('Please enter a reason');
    var dates, duration;
    if (f.mode === 'Multi Day') { dates = (f.start === f.end || !f.end) ? fmtDate(f.start) : fmtDate(f.start).slice(0, 6) + ' – ' + fmtDate(f.end); var a = new Date(f.start), b = new Date(f.end || f.start); duration = Math.max(1, Math.round((b - a) / 86400000) + 1) + ' days'; }
    else if (f.mode === 'Half Day') { dates = fmtDate(f.start); duration = '½ day · ' + f.half; }
    else if (f.mode === 'Short Leave') { dates = fmtDate(f.start); duration = t12(f.fromTime) + '–' + t12(f.toTime); }
    else { dates = fmtDate(f.start); duration = '1 day'; }
    state.leaves.unshift({ type: f.type, mode: f.mode, dates: dates, duration: duration, reason: f.reason, status: 'Pending', remarks: '—' });
    f.reason = ''; state.modal = null; TC.toast('Leave request submitted'); TC.render();
  }

  TC.models['filter-leave'] = function (v) { TC.state.leaveFilter = v; TC.render(); };
  Object.assign(TC.actions, {
    'open-leave': function () { TC.openModal('leave'); },
    'submit-leave': submitLeave
  });

  TC.boot(view);
  if (/[?&]new=1/.test(location.search)) TC.openModal('leave');
})();
