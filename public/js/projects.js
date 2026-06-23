/* ===== Projects page (list, detail, tasks, comments) ===== */
(function () {
  'use strict';
  var TC = window.TC, esc = TC.esc, badge = TC.badge, avatar = TC.avatar, I = TC.I, fmtDate = TC.fmtDate, TEAM = TC.TEAM;

  function recalc(p) { if (!p.tasks.length) return p.progress; return Math.round(p.tasks.filter(function (t) { return t.status === 'Done'; }).length / p.tasks.length * 100); }
  function bumpStatus(p) { if (p.tasks.length) p.status = p.progress === 100 ? 'Completed' : (p.status === 'Not Started' ? 'In Progress' : p.status); }

  function projectList() {
    var state = TC.state;
    var cards = state.projects.map(function (p, i) {
      var done = p.tasks.filter(function (t) { return t.status === 'Done'; }).length;
      var barCls = p.status === 'Completed' ? 'green' : p.status === 'On Hold' ? 'amber' : '';
      var chips = p.members.slice(0, 4).map(function (m) { return avatar(m, 26); }).join('');
      return '<div class="card card-pad clickable hover-pop" data-action="open-project" data-idx="' + i + '"><div class="spread" style="align-items:flex-start"><div style="min-width:0"><div style="font-size:15.5px;font-weight:800;color:#1E293B;letter-spacing:-.01em;line-height:1.3">' + esc(p.name) + '</div><div style="font-size:12.5px;color:#94A3B8;margin-top:2px">' + esc(p.role) + '</div></div>' + badge(p.status) + '</div><p style="font-size:13px;color:#64748B;margin-top:12px;line-height:1.55">' + esc(p.desc) + '</p><div style="margin-top:16px"><div class="spread" style="font-size:12px;color:#64748B;font-weight:600;margin-bottom:6px"><span>Progress · ' + done + '/' + p.tasks.length + ' tasks</span><span class="tc-num">' + p.progress + '%</span></div><div class="progress"><div class="progress-bar ' + barCls + '" style="width:' + p.progress + '%"></div></div></div><div class="row" style="margin-top:16px;padding-top:14px;border-top:1px solid #F1F5F9"><div><div class="kicker">DEADLINE</div><div style="font-size:13px;font-weight:700;color:#1E293B;margin-top:2px" class="tc-num">' + esc(p.deadline) + '</div></div><div><div class="kicker">PRIORITY</div><div style="margin-top:3px">' + badge(p.priority, 'sm') + '</div></div><div class="member-stack" style="margin-left:auto">' + chips + '</div></div></div>';
    }).join('');
    return '<div class="wrap"><div class="spread" style="margin-bottom:18px"><span class="section-title" style="margin-right:auto">All Projects</span><button class="btn btn-primary btn-sm" data-action="open-project-modal">' + I.plus + 'New Project</button></div><div class="cards-grid auto-330">' + cards + '</div></div>';
  }

  function projectDetail() {
    var state = TC.state;
    var p = state.projects[state.selectedProject];
    var done = p.tasks.filter(function (t) { return t.status === 'Done'; }).length;
    var todo = p.tasks.filter(function (t) { return t.status === 'To Do'; }).length;
    var prog = p.tasks.filter(function (t) { return t.status === 'In Progress'; }).length;
    var barCls = p.status === 'Completed' ? 'green' : p.status === 'On Hold' ? 'amber' : '';
    var chips = p.members.map(function (m) { return avatar(m, 30); }).join('');
    var tasks = p.tasks.map(function (t, ti) { return ti; }).filter(function (ti) { return state.taskFilter === 'All' || p.tasks[ti].status === state.taskFilter; }).map(function (ti) {
      var t = p.tasks[ti];
      var overdue = t.status !== 'Done' && /Jun 2026/.test(t.due) && parseInt(t.due) < 22;
      return '<div class="task-row" data-action="open-task" data-idx="' + ti + '"><div class="task-check' + (t.status === 'Done' ? ' done' : '') + '" data-action="toggle-task-done" data-idx="' + ti + '">' + (t.status === 'Done' ? I.check : '') + '</div><div style="flex:1;min-width:0"><div style="font-size:14px;font-weight:700;color:' + (t.status === 'Done' ? '#94A3B8' : '#1E293B') + ';text-decoration:' + (t.status === 'Done' ? 'line-through' : 'none') + '">' + esc(t.title) + '</div><div class="row flex-wrap" style="gap:14px;margin-top:6px"><span class="row" style="gap:6px">' + avatar(t.assignee, 26) + '<span style="font-size:12.5px;color:#64748B;font-weight:600">' + esc(t.assignee) + '</span></span><span class="row" style="gap:5px;font-size:12.5px;font-weight:700;color:' + (overdue ? '#C0392B' : '#475569') + '">' + I.cal + esc(t.due) + '</span>' + (t.comments.length ? '<span class="row" style="gap:5px;font-size:12.5px;color:#94A3B8;font-weight:600">' + I.comment + t.comments.length + '</span>' : '') + '</div></div>' + badge(t.priority, 'xs') + badge(t.status, 'sm') + '</div>';
    }).join('');
    var fopts = ['All', 'To Do', 'In Progress', 'Done'].map(function (o) { return '<option' + (o === state.taskFilter ? ' selected' : '') + '>' + o + '</option>'; }).join('');
    return '<div class="wrap"><button class="back-btn" data-action="close-project">' + I.back + 'All projects</button>' +
      '<div class="card" style="padding:24px;margin-bottom:18px"><div class="spread flex-wrap" style="align-items:flex-start"><div style="min-width:0;flex:1"><div class="row flex-wrap" style="gap:10px"><h2 style="font-size:22px;font-weight:800;letter-spacing:-.02em">' + esc(p.name) + '</h2>' + badge(p.status) + badge(p.priority, 'sm') + '</div><p style="font-size:13.5px;color:#64748B;margin-top:8px;line-height:1.55;max-width:620px">' + esc(p.desc) + '</p></div><button class="btn btn-primary btn-sm" data-action="open-task-modal">' + I.plus + 'New Task</button></div>' +
        '<div class="row flex-wrap" style="gap:28px;margin-top:20px;padding-top:18px;border-top:1px solid #F1F5F9"><div style="flex:1;min-width:180px"><div class="spread" style="font-size:12px;color:#64748B;font-weight:600;margin-bottom:6px"><span>Overall progress</span><span class="tc-num">' + p.progress + '%</span></div><div class="progress lg"><div class="progress-bar ' + barCls + '" style="width:' + p.progress + '%"></div></div></div><div><div class="kicker">DEADLINE</div><div style="font-size:14px;font-weight:700;color:#1E293B;margin-top:2px" class="tc-num">' + esc(p.deadline) + '</div></div><div><div class="kicker">MANAGER</div><div style="font-size:14px;font-weight:700;color:#1E293B;margin-top:2px">' + esc(p.mgr) + '</div></div><div><div class="kicker">TEAM</div><div class="member-stack" style="margin-top:5px">' + chips + '</div></div></div></div>' +
      '<div class="cols-3" style="margin-bottom:18px"><div class="card" style="padding:15px 18px"><div style="font-size:12px;color:#64748B;font-weight:700">To Do</div><div style="font-size:23px;font-weight:800;margin-top:5px;color:#5B6878" class="tc-num">' + todo + '</div></div><div class="card" style="padding:15px 18px"><div style="font-size:12px;color:#64748B;font-weight:700">In Progress</div><div style="font-size:23px;font-weight:800;margin-top:5px;color:#1763B6" class="tc-num">' + prog + '</div></div><div class="card" style="padding:15px 18px"><div style="font-size:12px;color:#64748B;font-weight:700">Done</div><div style="font-size:23px;font-weight:800;margin-top:5px;color:#1A7F44" class="tc-num">' + done + '</div></div></div>' +
      '<div class="card"><div class="spread" style="padding:16px 18px;border-bottom:1px solid #F1F5F9"><span class="section-title" style="margin-right:auto">Tasks</span><select class="select" data-model="filter-task" style="width:auto;padding:8px 11px;font-size:12.5px">' + fopts + '</select></div>' + (tasks || '<div style="padding:26px;text-align:center;color:#94A3B8;font-size:13.5px">No tasks yet. Add one with “New Task”.</div>') + '</div>' +
    '</div>';
  }

  function view() { return TC.state.selectedProject == null ? projectList() : projectDetail(); }

  // ----- modals -----
  TC.modals.task = function () {
    var state = TC.state, f = state.newTask;
    var p = state.projects[state.selectedProject];
    var members = (p ? p.members : ['Ayesha Khan']).map(function (m) { return '<option' + (m === f.assignee ? ' selected' : '') + '>' + esc(m) + '</option>'; }).join('');
    return TC.ov('<div class="modal" style="max-width:500px"><div class="modal-head"><h3>New Task</h3><button class="modal-x" data-action="close-modal">' + I.x + '</button></div><div class="modal-body"><label class="label">Task title</label><input class="input" data-model="newTask.title" placeholder="What needs to be done?" value="' + esc(f.title) + '" style="margin-bottom:16px"><div class="grid-2"><div><label class="label">Assign to</label><select class="select" data-model="newTask.assignee">' + members + '</select></div><div><label class="label">Due date</label><input type="date" class="input" data-model="newTask.due" value="' + f.due + '"></div><div><label class="label">Priority</label><select class="select" data-model="newTask.priority">' + ['Low', 'Medium', 'High', 'Critical'].map(function (o) { return '<option' + (o === f.priority ? ' selected' : '') + '>' + o + '</option>'; }).join('') + '</select></div><div><label class="label">Status</label><select class="select" data-model="newTask.status">' + ['To Do', 'In Progress', 'Done'].map(function (o) { return '<option' + (o === f.status ? ' selected' : '') + '>' + o + '</option>'; }).join('') + '</select></div></div></div><div class="modal-foot"><button class="btn btn-ghost" data-action="close-modal">Cancel</button><button class="btn btn-primary" data-action="create-task">Create task</button></div></div>');
  };

  TC.modals.taskDetail = function () {
    var state = TC.state;
    var p = state.projects[state.selectedProject];
    var t = p.tasks[state.activeTask];
    var comments = t.comments.map(function (c) { return '<div class="comment">' + avatar(c.by, 30) + '<div class="body"><div class="row" style="gap:8px"><span class="who">' + esc(c.by) + '</span><span class="when">' + esc(c.time) + '</span></div><div class="txt">' + esc(c.text) + '</div></div></div>'; }).join('');
    var statusOpts = ['To Do', 'In Progress', 'Done'].map(function (o) { return '<option' + (o === t.status ? ' selected' : '') + '>' + o + '</option>'; }).join('');
    return TC.ov('<div class="modal flex" style="max-width:560px"><div style="padding:22px 24px 18px;border-bottom:1px solid var(--line-2)"><div class="row" style="align-items:flex-start;gap:12px"><h3 style="font-size:19px;font-weight:800;letter-spacing:-.01em;line-height:1.3;flex:1">' + esc(t.title) + '</h3><button class="modal-x" data-action="close-task">' + I.x + '</button></div><div class="grid-2" style="margin-top:16px"><div><div class="kicker" style="margin-bottom:6px">ASSIGNEE</div><div class="row" style="gap:8px">' + avatar(t.assignee, 34) + '<span style="font-size:13.5px;font-weight:700;color:#1E293B">' + esc(t.assignee) + '</span></div></div><div><div class="kicker" style="margin-bottom:6px">DUE DATE</div><div class="row" style="gap:7px;font-size:13.5px;font-weight:700;color:#1E293B" class="tc-num">' + I.cal.replace('stroke="currentColor"', 'stroke="#64748B"').replace('width="13" height="13"', 'width="15" height="15"') + esc(t.due) + '</div></div><div><div class="kicker" style="margin-bottom:6px">PRIORITY</div>' + badge(t.priority, 'sm') + '</div><div><div class="kicker" style="margin-bottom:6px">STATUS</div><select class="select" data-model="set-task-status" style="width:auto;padding:7px 10px;font-size:12.5px;font-weight:700">' + statusOpts + '</select></div></div></div>' +
      '<div style="flex:1;overflow-y:auto;padding:20px 24px;background:#FAFCFE"><div style="font-size:12.5px;font-weight:800;color:#64748B;letter-spacing:.03em;margin-bottom:14px">COMMENTS · ' + t.comments.length + '</div>' + (t.comments.length ? '<div style="display:flex;flex-direction:column;gap:14px">' + comments + '</div>' : '<div style="font-size:13px;color:#94A3B8;text-align:center;padding:18px 0">No comments yet. Start the discussion below.</div>') + '</div>' +
      '<div class="comment-bar"><div class="avatar av-c0" style="width:32px;height:32px;font-size:12px">AK</div><input id="commentInput" placeholder="Write a comment…"><button class="send-btn" style="width:38px;height:38px" data-action="add-comment">' + I.send + '</button></div></div>');
  };

  TC.modals.project = function () {
    var state = TC.state, f = state.newProject;
    var mgrOpts = TEAM.map(function (t) { return '<option' + (t.name === f.mgr ? ' selected' : '') + '>' + esc(t.name) + '</option>'; }).join('');
    var names = ['Ayesha Khan'].concat(TEAM.map(function (t) { return t.name; })).filter(function (v, i, a) { return a.indexOf(v) === i; });
    var chips = names.map(function (n) { var sel = f.members.indexOf(n) >= 0; return '<div class="chip-pick' + (sel ? ' sel' : '') + '" data-action="toggle-member" data-name="' + esc(n) + '">' + avatar(n, 24) + esc(n) + '</div>'; }).join('');
    return TC.ov('<div class="modal"><div class="modal-head"><h3>New Project</h3><button class="modal-x" data-action="close-modal">' + I.x + '</button></div><div class="modal-body"><label class="label">Project name</label><input class="input" data-model="newProject.name" placeholder="e.g. Mobile App v2" value="' + esc(f.name) + '" style="margin-bottom:16px"><label class="label">Description</label><textarea class="textarea" data-model="newProject.desc" placeholder="What is this project about?" rows="2" style="margin-bottom:16px">' + esc(f.desc) + '</textarea><div class="grid-2"><div><label class="label">Project manager</label><select class="select" data-model="newProject.mgr">' + mgrOpts + '</select></div><div><label class="label">Deadline</label><input type="date" class="input" data-model="newProject.deadline" value="' + f.deadline + '"></div></div><label class="label" style="margin-top:14px">Priority</label><select class="select" data-model="newProject.priority">' + ['Low', 'Medium', 'High', 'Critical'].map(function (o) { return '<option' + (o === f.priority ? ' selected' : '') + '>' + o + '</option>'; }).join('') + '</select><label class="label" style="display:flex;align-items:center;gap:8px;margin:16px 0 9px">Team members<span class="badge xs badge-blue">' + f.members.length + ' selected</span></label><div style="display:flex;flex-wrap:wrap;gap:8px">' + chips + '</div></div><div class="modal-foot"><button class="btn btn-ghost" data-action="close-modal">Cancel</button><button class="btn btn-primary" data-action="create-project">Create project</button></div></div>');
  };

  // ----- actions -----
  function createTask() {
    var state = TC.state, f = state.newTask; if (!f.title.trim()) return TC.toast('Enter a task title');
    var p = state.projects[state.selectedProject];
    p.tasks.push({ title: f.title.trim(), assignee: f.assignee, due: fmtDate(f.due), status: f.status, priority: f.priority, comments: [] });
    p.progress = recalc(p); bumpStatus(p);
    f.title = ''; state.modal = null; TC.toast('Task created'); TC.render();
  }
  function createProject() {
    var state = TC.state, f = state.newProject; if (!f.name.trim()) return TC.toast('Enter a project name');
    var members = f.members.slice(); if (members.indexOf(f.mgr) < 0) members.push(f.mgr);
    state.projects.push({ name: f.name.trim(), role: 'Member', desc: f.desc.trim() || 'No description yet.', progress: 0, status: 'Not Started', deadline: f.deadline ? fmtDate(f.deadline) : 'TBD', priority: f.priority, mgr: f.mgr, members: members, tasks: [] });
    state.selectedProject = state.projects.length - 1; state.modal = null;
    state.newProject = { name: '', desc: '', mgr: 'Hamza Sheikh', deadline: '2026-09-30', priority: 'Medium', members: ['Ayesha Khan'] };
    TC.toast('Project created'); TC.render();
  }
  function addComment() {
    var state = TC.state;
    var inp = document.getElementById('commentInput'); var text = inp ? inp.value.trim() : '';
    if (!text) return;
    var t = state.projects[state.selectedProject].tasks[state.activeTask];
    t.comments.push({ by: 'Ayesha Khan', text: text, time: 'Just now' }); TC.render();
  }

  Object.assign(TC.actions, {
    'open-project': function (el, e, idx) { var s = TC.state; s.selectedProject = +idx; s.taskFilter = 'All'; TC.render(); },
    'close-project': function () { var s = TC.state; s.selectedProject = null; s.activeTask = null; TC.render(); },
    'open-project-modal': function () { TC.openModal('project'); },
    'open-task-modal': function () { TC.openModal('task'); },
    'open-task': function (el, e, idx) { TC.state.activeTask = +idx; TC.openModal('taskDetail'); },
    'close-task': function () { TC.state.activeTask = null; TC.closeModal(); },
    'create-task': createTask,
    'create-project': createProject,
    'add-comment': addComment,
    'toggle-task-done': function (el, e, idx) {
      e.stopPropagation();
      var p = TC.state.projects[TC.state.selectedProject]; var t = p.tasks[+idx];
      t.status = t.status === 'Done' ? 'To Do' : 'Done'; p.progress = recalc(p); bumpStatus(p); TC.render();
    },
    'toggle-member': function (el) {
      var nm = el.getAttribute('data-name'); var arr = TC.state.newProject.members; var k = arr.indexOf(nm);
      if (k >= 0) arr.splice(k, 1); else arr.push(nm); TC.renderModal();
    }
  });

  TC.models['filter-task'] = function (v) { TC.state.taskFilter = v; TC.render(); };
  TC.models['set-task-status'] = function (v) {
    var p = TC.state.projects[TC.state.selectedProject]; var t = p.tasks[TC.state.activeTask];
    t.status = v; p.progress = recalc(p); bumpStatus(p); TC.render();
  };

  TC.keydownHooks.push(function (e) { if (e.target.id === 'commentInput' && e.key === 'Enter') { e.preventDefault(); addComment(); } });

  TC.boot(view);
})();
