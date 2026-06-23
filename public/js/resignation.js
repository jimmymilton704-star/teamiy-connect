/* ===== Resignation page ===== */
(function () {
  'use strict';
  var TC = window.TC, esc = TC.esc, I = TC.I, fmtDate = TC.fmtDate;

  function infoCell(k, v, num) { return '<div><div class="k">' + k + '</div><div class="v"' + (num ? ' class="tc-num"' : '') + '>' + esc(v) + '</div></div>'; }

  function resignSteps() {
    var rg = TC.state.resignation;
    var defs = [
      { title: 'Resignation Submitted', sub: rg ? 'On ' + rg.submittedOn : 'You submit your notice', st: 'done' },
      { title: 'Manager Review', sub: 'Awaiting acceptance from Hamza Sheikh', st: rg ? 'current' : 'todo' },
      { title: 'HR Approval & Notice', sub: 'People team confirms your notice period', st: 'todo' },
      { title: 'Exit & Clearance', sub: 'Asset return, dues and knowledge transfer', st: 'todo' },
      { title: 'Relieved', sub: rg ? 'Last working day · ' + rg.lastDay : 'Final settlement & experience letter', st: 'todo' }
    ];
    return defs.map(function (x, i) {
      return '<div class="tl-step"><div class="tl-rail"><div class="tl-dot ' + x.st + '">' + (x.st === 'done' ? I.check.replace('stroke-width="3.2"', 'stroke-width="3"') : '') + '</div>' + (i < defs.length - 1 ? '<div class="tl-line' + (rg && i < 1 ? ' done' : '') + '"></div>' : '') + '</div><div style="padding-bottom:16px"><div style="font-size:14px;font-weight:700;color:' + (x.st === 'todo' ? '#94A3B8' : '#1E293B') + '">' + esc(x.title) + '</div><div style="font-size:12.5px;color:#94A3B8;margin-top:2px">' + esc(x.sub) + '</div></div></div>';
    }).join('');
  }

  function view() {
    var state = TC.state;
    if (!state.resignation) {
      return '<div class="wrap-xs" style="display:flex;flex-direction:column;gap:18px">' +
        '<div class="card" style="padding:24px"><div class="row" style="gap:14px"><div style="width:48px;height:48px;border-radius:13px;background:#FDEEE2;color:#C2691B;display:flex;align-items:center;justify-content:center;flex:none">' + I.resign.replace(/width="18" height="18"/, 'width="24" height="24"') + '</div><div><div style="font-size:18px;font-weight:800;letter-spacing:-.01em">Submit your resignation</div><div style="font-size:13.5px;color:#64748B;margin-top:2px">Start your formal exit process through Teamiy Connect.</div></div></div><div style="background:#F8FAFC;border:1px solid #EEF2F7;border-radius:12px;padding:14px 16px;margin-top:18px;font-size:13px;color:#64748B;line-height:1.6">Your contract requires a <strong style="color:#334155">30-day notice period</strong>. Once submitted, your manager and HR will review your request. You can withdraw it any time before it is accepted.</div><div class="info-grid" style="grid-template-columns:repeat(auto-fit,minmax(160px,1fr));margin-top:16px;border:1px solid #EEF2F7;border-radius:12px;overflow:hidden">' + infoCell('JOINED', '12 Jan 2025', true) + infoCell('TENURE', '1 year, 5 months') + infoCell('NOTICE PERIOD', '30 days') + '</div><button class="btn btn-danger" style="margin-top:20px;background:linear-gradient(135deg,#C0392B,#D9534F);font-size:14px;padding:12px 20px" data-action="open-resign">' + I.resign.replace(/width="18" height="18"/, 'width="16" height="16"') + 'Submit Resignation</button></div>' +
        '<div class="card" style="padding:22px"><div class="section-title" style="margin-bottom:18px">How it works</div>' + resignSteps() + '</div>' +
      '</div>';
    }
    var rg = state.resignation;
    return '<div class="wrap-xs" style="display:flex;flex-direction:column;gap:18px">' +
      '<div style="background:linear-gradient(120deg,#7A2E26,#C0392B);border-radius:18px;padding:22px 24px;color:#fff;position:relative;overflow:hidden"><div class="spread flex-wrap" style="align-items:flex-start;position:relative"><div><span class="badge" style="background:rgba(255,255,255,.2);color:#fff">UNDER REVIEW</span><div style="font-size:21px;font-weight:800;margin-top:12px;letter-spacing:-.01em">Resignation submitted</div><div style="font-size:13.5px;color:rgba(255,255,255,.85);margin-top:4px">Submitted on ' + esc(rg.submittedOn) + ' · ' + rg.noticeDays + '-day notice</div></div><button class="btn" style="background:#fff;color:#C0392B" data-action="withdraw-resign">Withdraw</button></div><div class="row flex-wrap" style="gap:28px;margin-top:18px"><div><div style="font-size:11.5px;color:rgba(255,255,255,.7);font-weight:700">LAST WORKING DAY</div><div style="font-size:15px;font-weight:800;margin-top:2px" class="tc-num">' + esc(rg.lastDay) + '</div></div><div><div style="font-size:11.5px;color:rgba(255,255,255,.7);font-weight:700">REASON</div><div style="font-size:15px;font-weight:800;margin-top:2px">' + esc(rg.reason) + '</div></div></div></div>' +
      '<div class="card" style="padding:22px"><div class="section-title" style="margin-bottom:18px">Exit Progress</div>' + resignSteps() + '</div>' +
      (rg.notes ? '<div class="card card-pad-lg"><div style="font-size:12.5px;font-weight:800;color:#64748B;letter-spacing:.03em;margin-bottom:8px">YOUR NOTE</div><p style="font-size:14px;color:#475569;line-height:1.6">' + esc(rg.notes) + '</p></div>' : '') +
    '</div>';
  }

  TC.modals.resign = function () {
    var f = TC.state.resignForm;
    return TC.ov('<div class="modal"><div class="modal-head"><h3>Submit Resignation</h3><button class="modal-x" data-action="close-modal">' + I.x + '</button></div><div class="modal-body"><div style="background:#FFF7EF;border:1px solid #F6DEC8;border-radius:11px;padding:11px 14px;font-size:12.5px;color:#A85C2A;line-height:1.55;margin-bottom:18px">This starts your formal exit process. Your manager and HR will be notified.</div><div class="grid-2"><div><label class="label">Resignation date</label><input type="date" class="input" data-model="resignForm.date" value="' + f.date + '"></div><div><label class="label">Last working day</label><input type="date" class="input" data-model="resignForm.lastDay" value="' + f.lastDay + '"></div></div><label class="label" style="margin-top:14px">Reason</label><select class="select" data-model="resignForm.reason">' + ['Better opportunity elsewhere', 'Relocation', 'Higher studies', 'Health reasons', 'Personal reasons', 'Career change', 'Other'].map(function (o) { return '<option' + (o === f.reason ? ' selected' : '') + '>' + o + '</option>'; }).join('') + '</select><label class="label" style="margin-top:14px">Note to manager (optional)</label><textarea class="textarea" data-model="resignForm.notes" placeholder="Add any context you\'d like to share…" rows="3">' + esc(f.notes) + '</textarea></div><div class="modal-foot"><button class="btn btn-ghost" data-action="close-modal">Cancel</button><button class="btn btn-danger" style="background:linear-gradient(135deg,#C0392B,#D9534F)" data-action="submit-resign">Submit Resignation</button></div></div>');
  };

  function submitResign() {
    var state = TC.state, f = state.resignForm;
    var days = Math.max(0, Math.round((new Date(f.lastDay) - new Date(f.date)) / 86400000));
    state.resignation = { status: 'Under Review', submittedOn: fmtDate(f.date), lastDay: fmtDate(f.lastDay), reason: f.reason, notes: f.notes, noticeDays: days };
    state.modal = null; TC.toast('Resignation submitted'); TC.render();
  }

  Object.assign(TC.actions, {
    'open-resign': function () { TC.openModal('resign'); },
    'submit-resign': submitResign,
    'withdraw-resign': function () { TC.state.resignation = null; TC.toast('Resignation withdrawn'); TC.render(); }
  });

  TC.boot(view);
})();
