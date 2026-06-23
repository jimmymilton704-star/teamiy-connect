/* ===== Profile Settings page ===== */
(function () {
  'use strict';
  var TC = window.TC, esc = TC.esc, I = TC.I, colorClass = TC.colorClass, initials = TC.initials;

  function infoCell(k, v, num) { return '<div><div class="k">' + k + '</div><div class="v"' + (num ? ' class="tc-num"' : '') + '>' + esc(v) + '</div></div>'; }
  function field(label, model, val, type) { return '<label class="label">' + label + '</label><input class="input" type="' + (type || 'text') + '" data-model="profileForm.' + model + '" value="' + esc(val) + '" style="margin-bottom:14px">'; }

  function view() {
    var state = TC.state;
    var f = state.profileForm;
    var photo = state.profilePhoto;
    var avStyle = photo ? 'style="--g:none;background-image:url(' + photo + ')"' : '';
    var avInit = photo ? '' : initials(f.name);
    return '<div class="wrap-sm" style="display:grid;grid-template-columns:1fr;gap:18px">' +
      '<div class="card" style="overflow:hidden;border-radius:18px"><div class="profile-cover"></div><div style="padding:0 24px 22px;margin-top:-38px"><div class="row flex-wrap" style="align-items:flex-end;gap:18px"><div class="profile-av ' + colorClass(f.name) + '" ' + avStyle + '>' + avInit + '</div><div style="flex:1;min-width:0;padding-bottom:4px"><div style="font-size:20px;font-weight:800">' + esc(f.name) + '</div><div style="font-size:13.5px;color:#64748B">Senior Frontend Engineer · Product Engineering</div></div><span class="badge badge-green" style="align-self:center">Active</span></div></div>' +
        '<div class="info-grid">' + infoCell('EMPLOYEE CODE', 'TMY-0428', true) + infoCell('EMAIL', f.email) + infoCell('REPORTING TO', 'Hamza Sheikh') + infoCell('JOINED', '12 Jan 2025', true) + '</div></div>' +
      '<div class="grid-2">' +
        '<div class="card card-pad-lg"><div class="section-title" style="margin-bottom:16px">Editable Details</div>' +
          '<label class="label">Profile photo</label><div class="row" style="gap:12px;margin-bottom:16px"><div class="avatar ' + colorClass(f.name) + '" ' + (photo ? 'style="--g:none;background-image:url(' + photo + ')"' : '') + ' style="width:48px;height:48px;border-radius:13px;font-size:16px;background-size:cover">' + (photo ? '' : initials(f.name)) + '</div><label class="btn btn-sm" style="background:#EAF5FB;color:var(--primary)">' + I.upload + 'Upload new photo<input type="file" accept="image/*" data-action="photo-change" style="display:none"></label></div>' +
          field('Full name', 'name', f.name) + field('Email', 'email', f.email, 'email') + field('Phone', 'phone', f.phone) +
          '<label class="label">Emergency contact name</label><input class="input" data-model="profileForm.emName" value="' + esc(f.emName) + '" style="margin-bottom:14px">' +
          '<label class="label">Emergency contact phone</label><input class="input" data-model="profileForm.emPhone" value="' + esc(f.emPhone) + '" style="margin-bottom:14px">' +
          '<label class="label">Personal address</label><textarea class="textarea" data-model="profileForm.address" rows="2" style="margin-bottom:18px">' + esc(f.address) + '</textarea>' +
          '<button class="btn btn-primary" data-action="save-profile">Save changes</button></div>' +
        '<div style="display:flex;flex-direction:column;gap:18px">' +
          '<div class="card card-pad-lg"><div class="section-title" style="margin-bottom:16px">Bank Details</div><div class="grid-2"><div>' + field('Bank name', 'bankName', f.bankName) + '</div><div><label class="label">Account number</label><input class="input tc-num" data-model="profileForm.accountNumber" value="' + esc(f.accountNumber) + '"></div><div><label class="label">Account type</label><select class="select" data-model="profileForm.accountType">' + ['Savings', 'Current', 'Salary'].map(function (o) { return '<option' + (o === f.accountType ? ' selected' : '') + '>' + o + '</option>'; }).join('') + '</select></div></div><button class="btn btn-primary" style="margin-top:18px" data-action="save-profile">Save details</button></div>' +
          '<div class="card card-pad-lg"><div class="section-title" style="margin-bottom:16px">Change Password</div><input class="input" type="password" placeholder="Current password" style="margin-bottom:12px"><input class="input" type="password" placeholder="New password" style="margin-bottom:12px"><input class="input" type="password" placeholder="Confirm new password" style="margin-bottom:16px"><button class="btn btn-dark" data-action="save-password">Update password</button></div>' +
        '</div>' +
      '</div>' +
    '</div>';
  }

  Object.assign(TC.actions, {
    'save-profile': function () { TC.toast('Profile updated successfully'); },
    'save-password': function () { TC.toast('Password updated'); }
  });

  TC.changeActions['photo-change'] = function (el, e) {
    var file = e.target.files && e.target.files[0];
    if (file) { var rd = new FileReader(); rd.onload = function () { TC.state.profilePhoto = rd.result; TC.toast('Photo updated'); TC.render(); }; rd.readAsDataURL(file); }
  };

  TC.boot(view);
})();
