/* ===== Inbox / chat page ===== */
(function () {
  'use strict';
  var TC = window.TC, esc = TC.esc, avatar = TC.avatar, I = TC.I, fmtTime = TC.fmtTime;

  function bubbleHtml(b, bi) {
    var state = TC.state;
    var me = b.me ? 'me' : 'them';
    var inner;
    if (b.kind === 'text') inner = '<div class="bubble ' + me + '">' + esc(b.text) + '</div>';
    else if (b.kind === 'voice') {
      var playing = state.playingVoice === (state.activeMessage + '-' + bi);
      var heights = [10, 16, 8, 20, 13, 7, 18, 11, 22, 9, 15, 12, 19, 8, 14, 10, 17, 9];
      var bars = heights.map(function (h, k) { return '<i class="' + (!b.me && k < (playing ? 18 : 7) ? 'on' : '') + '" style="height:' + h + 'px"></i>'; }).join('');
      inner = '<div class="voice ' + me + '"><button class="play" data-action="toggle-voice" data-idx="' + bi + '"><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="' + (playing ? 'M6 5h4v14H6zM14 5h4v14h-4z' : 'M7 5l12 7-12 7z') + '"/></svg></button><div class="wave">' + bars + '</div><span class="vdur">' + b.dur + '</span></div>';
    } else if (b.kind === 'image') {
      inner = '<div class="img-bubble"><div class="ph"><svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="8.5" cy="9.5" r="1.8"/><path d="M21 16l-5-5L5 20"/></svg></div><div class="cap">' + esc(b.cap) + '</div></div>';
    } else if (b.kind === 'file') {
      inner = '<div class="attach ' + me + '"><div class="fico">' + I.file + '</div><div style="min-width:0;flex:1"><div class="fnm">' + esc(b.fileName) + '</div><div class="fsz">' + esc(b.fileSize) + '</div></div></div>';
    }
    return '<div class="bubble-row ' + me + '">' + inner + '<span class="btime">' + esc(b.time) + '</span></div>';
  }

  function view() {
    var state = TC.state;
    var rows = state.messages.map(function (m, i) {
      var last = m.thread[m.thread.length - 1] || {};
      var prev = last.kind === 'voice' ? '🎤 Voice message' : last.kind === 'image' ? '📷 Photo' : last.kind === 'file' ? '📎 ' + last.fileName : (last.me ? 'You: ' : '') + last.text;
      return '<div class="chat-row' + (i === state.activeMessage ? ' active' : '') + '" data-action="open-chat" data-idx="' + i + '"><div class="av-wrap">' + avatar(m.from, 46) + '<span class="online-dot' + (m.online ? ' on' : '') + '"></span></div><div style="flex:1;min-width:0"><div class="spread"><span style="font-size:13.5px;font-weight:' + (m.unread ? 800 : 700) + ';color:#1E293B;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;flex:1">' + esc(m.from) + '</span><span style="font-size:11px;color:#94A3B8;flex:none">' + esc(last.time || '') + '</span></div><div class="spread" style="margin-top:3px"><span style="font-size:12.5px;font-weight:' + (m.unread ? 700 : 500) + ';color:' + (m.unread ? '#334155' : '#94A3B8') + ';white-space:nowrap;overflow:hidden;text-overflow:ellipsis;flex:1">' + esc(prev) + '</span>' + (m.unread ? '<span class="chat-unread">' + m.unread + '</span>' : '') + '</div></div></div>';
    }).join('');
    var am = state.messages[state.activeMessage];
    var bubbles = am.thread.map(function (b, bi) { return bubbleHtml(b, bi); }).join('');
    var composer;
    if (state.recording) {
      composer = '<div class="recording"><span class="rdot"></span><span style="font-size:13.5px;font-weight:700;color:#C0392B;flex:1">Recording… tap to send</span><button class="btn btn-danger btn-sm" data-action="toggle-record"><svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><rect x="6" y="6" width="12" height="12" rx="2"/></svg>Stop &amp; Send</button></div>';
    } else {
      composer = '<div class="composer-row"><label class="round" title="Attach file">' + I.clip + '<input type="file" data-action="send-attachment" style="display:none"></label><button class="round" data-action="send-image" title="Send photo">' + I.photo + '</button><textarea id="chatInput" placeholder="Type a message…" rows="1"></textarea><button class="send-btn" id="chatSend" data-action="send-draft" title="Send" style="display:none">' + I.send + '</button><button class="send-btn" id="chatMic" data-action="toggle-record" title="Record voice">' + I.mic + '</button></div>';
    }
    return '<div class="inbox"><div class="chat-list"><div class="chat-list-head"><span class="section-title" style="margin-right:auto">Chats</span><span style="font-size:12px;color:#94A3B8;font-weight:600">' + state.messages.length + ' chats</span><button class="link" style="font-size:12px;white-space:nowrap" data-action="mark-all-inbox">Mark all read</button></div><div class="chat-scroll">' + rows + '</div></div>' +
      '<div class="chat-pane"><div class="chat-head">' + avatar(am.from, 42) + '<div style="flex:1;min-width:0"><div style="font-size:15px;font-weight:800;color:#1E293B">' + esc(am.from) + '</div><div class="row" style="gap:6px;font-size:12px;font-weight:600;color:' + (am.online ? '#16A34A' : '#94A3B8') + '"><span style="width:7px;height:7px;border-radius:50%;background:currentColor"></span>' + (am.online ? 'Active now' : 'Offline') + '</div></div></div>' +
      '<div class="chat-thread" id="chatThread"><div class="day-sep"><span>Today</span></div>' + bubbles + '</div>' +
      '<div class="composer">' + composer + '</div></div></div>';
  }

  function sendChat(item, msg) {
    var entry = Object.assign({ me: true, time: fmtTime(new Date()) }, item);
    TC.state.messages[TC.state.activeMessage].thread.push(entry);
    if (msg) TC.toast(msg); TC.render();
  }

  Object.assign(TC.actions, {
    'open-chat': function (el, e, idx) { var s = TC.state; s.activeMessage = +idx; s.messages[+idx].unread = 0; s.playingVoice = null; TC.render(); },
    'toggle-voice': function (el, e, idx) { var s = TC.state; var key = s.activeMessage + '-' + idx; s.playingVoice = s.playingVoice === key ? null : key; TC.render(); },
    'send-draft': function () { var ci = document.getElementById('chatInput'); if (ci && ci.value.trim()) sendChat({ kind: 'text', text: ci.value.trim() }); },
    'toggle-record': function () { var s = TC.state; if (s.recording) { s.recording = false; sendChat({ kind: 'voice', dur: '0:08' }, 'Voice message sent'); } else { s.recording = true; TC.render(); } },
    'send-image': function () { sendChat({ kind: 'image', cap: 'Photo' }, 'Photo sent'); },
    'mark-all-inbox': function () { TC.state.messages.forEach(function (m) { m.unread = 0; }); TC.toast('All chats marked read'); TC.render(); }
  });

  TC.changeActions['send-attachment'] = function (el, e) {
    if (e.target.files && e.target.files[0]) sendChat({ kind: 'file', fileName: e.target.files[0].name || 'Document.pdf', fileSize: Math.max(1, Math.round((e.target.files[0].size || 188000) / 1024)) + ' KB' }, 'Attachment sent');
  };

  // toggle send/mic buttons as the user types
  TC.inputHooks.push(function (e) {
    if (e.target.id !== 'chatInput') return;
    var send = document.getElementById('chatSend'), mic = document.getElementById('chatMic');
    if (send && mic) { var has = e.target.value.trim().length > 0; send.style.display = has ? 'flex' : 'none'; mic.style.display = has ? 'none' : 'flex'; }
  });
  TC.keydownHooks.push(function (e) {
    if (e.target.id === 'chatInput' && e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); var v = e.target.value.trim(); if (v) sendChat({ kind: 'text', text: v }); }
  });

  function scrollThread() { var th = document.getElementById('chatThread'); if (th) th.scrollTop = th.scrollHeight; }

  TC.boot(view, { after: scrollThread });
})();
