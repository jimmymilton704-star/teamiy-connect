@extends('layouts.app')

@section('title', 'Projects - Teamiy Connect')
@section('page', 'projects')
@section('page_title', 'Projects')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/projects.css') }}">

    <style>
        .tc-pagination {
            margin-top: 18px;
        }

        .tc-pagination nav {
            display: flex;
            justify-content: center;
        }

        .tc-pagination nav>div:first-child {
            display: none !important;
        }

        .tc-pagination nav>div:last-child {
            display: flex !important;
            justify-content: center;
            align-items: center;
            width: 100%;
        }

        .tc-pagination nav>div:last-child>div:first-child {
            display: none !important;
        }

        .tc-pagination nav>div:last-child>div:last-child {
            display: flex !important;
            align-items: center;
            justify-content: center;
        }

        .tc-pagination nav>div:last-child>div:last-child>span {
            display: flex !important;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .tc-pagination a,
        .tc-pagination span span,
        .tc-pagination span[aria-current="page"] span {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            min-width: 38px;
            height: 38px;
            border-radius: 10px;
            border: 1px solid #E2E8F0;
            background: #FFFFFF;
            color: #475569;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
        }

        .tc-pagination a:hover {
            background: #F47B26 !important;
            border-color: #F47B26 !important;
            color: #FFFFFF !important;
        }

        .tc-pagination span[aria-current="page"] span {
            background: #F47B26 !important;
            border-color: #F47B26 !important;
            color: #FFFFFF !important;
        }

        .tc-pagination span[aria-disabled="true"] span {
            opacity: .45;
            cursor: not-allowed;
        }

        .member-stack {
            display: flex;
            align-items: center;
        }

        .member-stack .avatar {
            margin-left: -6px;
            border: 2px solid #fff;
        }

        .member-stack .avatar:first-child {
            margin-left: 0;
        }

        .avatar {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-weight: 900;
            color: #fff;
            flex: none;
        }

        .av-c0 { background: #3498DB; }
        .av-c1 { background: #805AD5; }
        .av-c2 { background: #E28400; }
        .av-c3 { background: #319C7A; }
        .av-c4 { background: #C65378; }
        .av-c5 { background: #456FC5; }

        .task-modal-overlay {
            position: fixed;
            inset: 0;
            z-index: 99999;
            background: rgba(15, 23, 42, .45);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .task-modal {
            width: min(560px, 100%);
            background: #fff;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 30px 80px rgba(15, 23, 42, .32);
        }

        .task-modal-head {
            padding: 22px 24px 16px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            border-bottom: 1px solid #F1F5F9;
        }

        .task-modal-head h3 {
            flex: 1;
            font-size: 19px;
            font-weight: 900;
            color: #0F172A;
            letter-spacing: -.01em;
            margin: 0;
        }

        .task-modal-x {
            border: 0;
            background: transparent;
            color: #94A3B8;
            font-size: 28px;
            line-height: 1;
            cursor: pointer;
        }

        .task-modal-body {
            padding: 20px 24px;
        }

        .task-modal-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px 28px;
        }

        .checklist-box {
            margin-top: 16px;
            padding: 14px;
            border-radius: 14px;
            background: #F8FAFC;
            border: 1px solid #EEF2F7;
        }

        .checklist-item {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            padding: 8px 0;
            border-bottom: 1px solid #EEF2F7;
        }

        .checklist-item:last-child {
            border-bottom: 0;
        }

        .checklist-dot {
            width: 21px;
            height: 21px;
            border-radius: 50%;
            border: 2px solid #CBD5E1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 900;
            color: #fff;
            flex: none;
        }

        .checklist-dot.done {
            background: #1A7F44;
            border-color: #1A7F44;
        }

        .task-modal-comments {
            padding: 20px 24px;
            background: #FAFCFE;
            border-top: 1px solid #F1F5F9;
            max-height: 260px;
            overflow-y: auto;
        }

        .task-comment {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            margin-bottom: 14px;
        }

        .task-comment .body {
            min-width: 0;
            flex: 1;
        }

        .task-comment .who {
            font-size: 13px;
            font-weight: 900;
            color: #1E293B;
        }

        .task-comment .when {
            font-size: 12px;
            color: #94A3B8;
            font-weight: 700;
        }

        .task-comment .txt {
            font-size: 13px;
            color: #64748B;
            margin-top: 3px;
            line-height: 1.45;
        }

        .comment-bar {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 14px 18px;
            border-top: 1px solid #F1F5F9;
            background: #fff;
        }

        .comment-bar input {
            flex: 1;
            height: 40px;
            border: 1px solid #DDE6F1;
            border-radius: 999px;
            padding: 0 16px;
            outline: 0;
            font-size: 13px;
            color: #334155;
        }

        .send-btn {
            width: 40px;
            height: 40px;
            border: 0;
            border-radius: 50%;
            background: #057DB0;
            color: #fff;
            font-size: 17px;
            cursor: pointer;
        }

        @media (max-width: 640px) {
            .task-modal-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $taskPayload = $tasks->values()->map(function ($task) {
            $assignees = $task->checklists
                ->pluck('assignee.name')
                ->filter()
                ->unique()
                ->values();

            if ($assignees->isEmpty()) {
                $assignees = $task->assignees->pluck('name')->filter()->unique()->values();
            }

            return [
                'id' => $task->id,
                'name' => $task->name,
                'project' => $task->project->name ?? '-',
                'priority' => ucfirst((string) ($task->priority ?: 'Medium')),
                'status' => $task->status,
                'due' => optional($task->end_date)->format('d M Y') ?: '-',
                'description' => strip_tags((string) $task->description),
                'assignees' => $assignees->isNotEmpty() ? $assignees : collect(['Unassigned']),
                'checklists' => $task->checklists->map(fn ($checklist) => [
                    'name' => $checklist->name,
                    'assignee' => $checklist->assignee->name ?? 'Unassigned',
                    'is_completed' => (bool) $checklist->is_completed,
                ])->values(),
                'comments' => $task->comments->map(fn ($comment) => [
                    'id' => $comment->id,
                    'description' => $comment->description,
                    'creator' => $comment->creator->name ?? 'User',
                    'created_at' => optional($comment->created_at)->diffForHumans() ?: '',
                ])->values(),
            ];
        });
    @endphp

    <div class="wrap">

        <div class="spread" style="margin-bottom:18px">
            <span class="section-title" style="margin-right:auto">All Projects</span>
        </div>

        <div class="cards-grid auto-330">
            @forelse($projects as $project)
                @php
                    $totalTasks = $project->tasks_count ?? 0;
                    $doneTasks = $project->tasks_done_count ?? 0;
                    $progress = $totalTasks > 0 ? round(($doneTasks / $totalTasks) * 100) : 0;

                    $projectStatus = strtolower(str_replace(' ', '_', $project->status));

                    if ($progress >= 100) {
                        $displayStatus = 'Done';
                        $projectBadgeClass = 'badge-green';
                        $barClass = 'green';
                    } else {
                        $displayStatus = ucfirst(str_replace('_', ' ', $project->status));

                        $projectBadgeClass = match ($projectStatus) {
                            'completed', 'done' => 'badge-green',
                            'in_progress', 'active' => 'badge-blue',
                            'on_hold', 'pending', 'to_do' => 'badge-amber',
                            default => 'badge-gray',
                        };

                        $barClass = match ($projectStatus) {
                            'completed', 'done' => 'green',
                            'on_hold' => 'amber',
                            default => '',
                        };
                    }
                @endphp

                <div class="card card-pad clickable hover-pop"
                     onclick="window.location.href='{{ route('projects.show', $project->id) }}'"
                     style="cursor:pointer">

                    <div class="spread" style="align-items:flex-start">
                        <div style="min-width:0">
                            <div style="font-size:15.5px;font-weight:800;color:#1E293B;letter-spacing:-.01em;line-height:1.3">
                                {{ $project->name }}
                            </div>

                            <div style="font-size:12.5px;color:#94A3B8;margin-top:2px">
                                Project
                            </div>
                        </div>

                        <span class="badge sm {{ $projectBadgeClass }}">
                            {{ $displayStatus }}
                        </span>
                    </div>

                    <p style="font-size:13px;color:#64748B;margin-top:12px;line-height:1.55">
                        {{ str($project->description)->stripTags()->limit(130) }}
                    </p>

                    <div style="margin-top:16px">
                        <div class="spread" style="font-size:12px;color:#64748B;font-weight:600;margin-bottom:6px">
                            <span>Progress · {{ $doneTasks }}/{{ $totalTasks }} tasks</span>
                            <span class="tc-num">{{ $progress }}%</span>
                        </div>

                        <div class="progress">
                            <div class="progress-bar {{ $barClass }}" style="width:{{ $progress }}%"></div>
                        </div>
                    </div>

                    <div class="row" style="margin-top:16px;padding-top:14px;border-top:1px solid #F1F5F9">
                        <div>
                            <div class="kicker">DEADLINE</div>
                            <div style="font-size:13px;font-weight:700;color:#1E293B;margin-top:2px" class="tc-num">
                                {{ optional($project->deadline)->format('d M Y') ?: 'TBD' }}
                            </div>
                        </div>

                        <div>
                            <div class="kicker">START</div>
                            <div style="font-size:13px;font-weight:700;color:#1E293B;margin-top:2px" class="tc-num">
                                {{ optional($project->start_date)->format('d M Y') ?: '-' }}
                            </div>
                        </div>

                        <div>
                            <div class="kicker">TASKS</div>
                            <div style="font-size:13px;font-weight:700;color:#1E293B;margin-top:2px" class="tc-num">
                                {{ $totalTasks }}
                            </div>
                        </div>

                        <div class="member-stack" style="margin-left:auto">
                            @forelse($project->members->take(4) as $member)
                                @php
                                    $parts = collect(explode(' ', (string) $member->name))->filter();
                                    $initials = $parts->map(fn($part) => strtoupper(mb_substr($part, 0, 1)))->take(2)->implode('') ?: 'TM';
                                @endphp

                                <span class="avatar av-c{{ $loop->index % 6 }}"
                                      title="{{ $member->name }}"
                                      style="width:26px;height:26px;font-size:10px">
                                    {{ $initials }}
                                </span>
                            @empty
                                <span style="font-size:12px;color:#94A3B8;font-weight:700">
                                    No members
                                </span>
                            @endforelse

                            @if ($project->members->count() > 4)
                                <span class="avatar"
                                      style="width:26px;height:26px;font-size:10px;background:#E2E8F0;color:#475569">
                                    +{{ $project->members->count() - 4 }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="card card-pad" style="text-align:center;color:#94A3B8;font-size:13.5px">
                    No projects found.
                </div>
            @endforelse
        </div>

        <div class="tc-pagination">
            {{ $projects->onEachSide(1)->links() }}
        </div>

        <div class="card" style="margin-top:18px">
            <div class="spread" style="padding:16px 18px;border-bottom:1px solid #F1F5F9">
                <span class="section-title" style="margin-right:auto">My Tasks</span>
            </div>

            @forelse($tasks as $task)
                @php
                    $isDone = in_array($task->status, ['done', 'Done', 'completed', 'Completed']);

                    $taskStatus = strtolower(str_replace(' ', '_', $task->status));

                    $taskBadgeClass = match ($taskStatus) {
                        'done', 'completed' => 'badge-green',
                        'in_progress' => 'badge-blue',
                        'to_do', 'pending' => 'badge-amber',
                        default => 'badge-gray',
                    };

                    $firstAssignee = $task->checklists->pluck('assignee.name')->filter()->first()
                        ?: $task->assignees->pluck('name')->filter()->first()
                        ?: 'Unassigned';

                    $parts = collect(explode(' ', $firstAssignee))->filter();
                    $initials = $parts->map(fn($part) => strtoupper(mb_substr($part, 0, 1)))->take(2)->implode('') ?: 'NA';
                @endphp

                <div class="task-row" data-task-id="{{ $task->id }}" onclick="openTaskModal({{ $task->id }})">

                    <div class="task-check {{ $isDone ? 'done' : '' }}"
                         onclick="toggleTaskDone(event, this)"
                         data-task-id="{{ $task->id }}"
                         style="cursor:pointer">
                        @if ($isDone)
                            ✓
                        @endif
                    </div>

                    <div style="flex:1;min-width:0">
                        <div 
                             style="font-size:14px;font-weight:700;color:{{ $isDone ? '#94A3B8' : '#1E293B' }};text-decoration:{{ $isDone ? 'line-through' : 'none' }};cursor:pointer">
                            {{ $task->name }}
                        </div>

                        <div class="row flex-wrap" style="gap:14px;margin-top:6px">
                            <span style="font-size:12.5px;color:#64748B;font-weight:600">
                                {{ $task->project->name ?? '-' }}
                            </span>

                            <span class="row" style="gap:6px">
                                <span class="avatar av-c0" style="width:24px;height:24px;font-size:9px">
                                    {{ $initials }}
                                </span>

                                <span style="font-size:12.5px;color:#64748B;font-weight:600">
                                    {{ $firstAssignee }}
                                </span>
                            </span>

                            <span style="font-size:12.5px;color:#475569;font-weight:700">
                                Due {{ optional($task->end_date)->format('d M Y') ?: '-' }}
                            </span>

                            @if($task->comments->count())
                                <span style="font-size:12.5px;color:#94A3B8;font-weight:700">
                                    💬 {{ $task->comments->count() }}
                                </span>
                            @endif
                        </div>
                    </div>

                    @if ($task->priority)
                        <span class="badge xs badge-blue">
                            {{ ucfirst($task->priority) }}
                        </span>
                    @endif

                    <span class="badge sm {{ $taskBadgeClass }}">
                        {{ $isDone ? 'Done' : ucfirst(str_replace('_', ' ', $task->status)) }}
                    </span>
                </div>
            @empty
                <div style="padding:26px;text-align:center;color:#94A3B8;font-size:13.5px">
                    No assigned tasks found.
                </div>
            @endforelse
        </div>

    </div>

    <div id="taskModalRoot" style="display:none">
        <div class="task-modal-overlay" onclick="closeTaskModal(event)">
            <div class="task-modal" onclick="event.stopPropagation()">
                <div class="task-modal-head">
                    <h3 id="modalTaskTitle">Task detail</h3>

                    <button type="button" class="task-modal-x" onclick="hideTaskModal()">
                        ×
                    </button>
                </div>

                <div class="task-modal-body">
                    <div class="task-modal-grid">
                        <div>
                            <div class="kicker">PROJECT</div>
                            <div id="modalProjectName" style="font-size:13.5px;font-weight:800;color:#1E293B;margin-top:8px"></div>
                        </div>

                        <div>
                            <div class="kicker">DUE DATE</div>
                            <div id="modalDueDate" style="font-size:13.5px;font-weight:800;color:#1E293B;margin-top:8px"></div>
                        </div>

                        <div>
                            <div class="kicker">ASSIGNEE</div>
                            <div id="modalAssignees" style="margin-top:8px"></div>
                        </div>

                        <div>
                            <div class="kicker">STATUS</div>
                            <select id="modalTaskStatus"
                                    class="select"
                                    onchange="updateTaskStatusFromModal()"
                                    style="width:130px;margin-top:8px;padding:8px 10px;font-size:12.5px">
                                <option>To Do</option>
                                <option>In Progress</option>
                                <option>Done</option>
                            </select>
                        </div>

                        <div>
                            <div class="kicker">PRIORITY</div>
                            <div id="modalPriority" style="margin-top:8px"></div>
                        </div>
                    </div>

                    <div id="modalDescriptionWrap" style="display:none;margin-top:18px">
                        <div class="kicker">DESCRIPTION</div>
                        <div id="modalDescription"
                             style="font-size:13px;color:#64748B;line-height:1.55;margin-top:8px"></div>
                    </div>

                    <div class="checklist-box">
                        <div id="modalChecklistTitle"
                             style="font-size:12.5px;font-weight:900;color:#64748B;letter-spacing:.03em;margin-bottom:8px">
                            CHECKLISTS
                        </div>

                        <div id="modalChecklists"></div>
                    </div>
                </div>

                <div class="task-modal-comments">
                    <div id="modalCommentTitle"
                         style="font-size:12.5px;font-weight:900;color:#64748B;letter-spacing:.03em;margin-bottom:14px">
                        COMMENTS
                    </div>

                    <div id="modalComments"></div>
                </div>

                <div class="comment-bar">
                    <span class="avatar av-c0" style="width:32px;height:32px;font-size:12px">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </span>

                    <input id="commentInput" placeholder="Write a comment...">

                    <button class="send-btn" type="button" onclick="storeTaskComment()">
                        ➤
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const TASKS = @json($taskPayload);
    let activeTaskId = null;

    function toggleTaskDone(event, checkEl) {
        event.stopPropagation();

        const taskId = checkEl.dataset.taskId;

        fetch(`/tasks/${taskId}/toggle-status`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (!data.status) return;
            location.reload();
        })
        .catch(() => {
            alert('Task status update failed.');
        });
    }

    function openTaskModal(taskId) {
        const task = TASKS.find(item => Number(item.id) === Number(taskId));
        if (!task) return;

        activeTaskId = task.id;

        document.getElementById('modalTaskTitle').innerText = task.name;
        document.getElementById('modalProjectName').innerText = task.project;
        document.getElementById('modalDueDate').innerText = task.due;
        document.getElementById('modalTaskStatus').value = normalizeTaskStatus(task.status);

        document.getElementById('modalPriority').innerHTML =
            `<span class="badge xs badge-blue">${escapeHtml(task.priority)}</span>`;

        document.getElementById('modalAssignees').innerHTML = task.assignees.map((name, index) => {
            const initials = getInitials(name);

            return `
                <div class="row" style="gap:8px;margin-bottom:6px">
                    <span class="avatar av-c${index % 6}" style="width:30px;height:30px;font-size:11px">${initials}</span>
                    <span style="font-size:13.5px;font-weight:800;color:#1E293B">${escapeHtml(name)}</span>
                </div>
            `;
        }).join('');

        const descWrap = document.getElementById('modalDescriptionWrap');
        const desc = document.getElementById('modalDescription');

        if (task.description) {
            descWrap.style.display = 'block';
            desc.innerText = task.description;
        } else {
            descWrap.style.display = 'none';
            desc.innerText = '';
        }

        document.getElementById('modalChecklistTitle').innerText =
            `CHECKLISTS · ${task.checklists.length}`;

        document.getElementById('modalChecklists').innerHTML = task.checklists.length
            ? task.checklists.map(item => `
                <div class="checklist-item">
                    <span class="checklist-dot ${item.is_completed ? 'done' : ''}">
                        ${item.is_completed ? '✓' : ''}
                    </span>

                    <div style="min-width:0;flex:1">
                        <div style="font-size:13px;font-weight:800;color:#1E293B">
                            ${escapeHtml(item.name)}
                        </div>
                        <div style="font-size:12.5px;color:#64748B;margin-top:3px">
                            Assigned to ${escapeHtml(item.assignee)}
                        </div>
                    </div>
                </div>
            `).join('')
            : `<div style="font-size:13px;color:#94A3B8;text-align:center;padding:10px 0">
                    No checklist found.
               </div>`;

        renderComments(task.comments || []);

        document.getElementById('commentInput').value = '';
        document.getElementById('taskModalRoot').style.display = 'block';
    }

    function renderComments(comments) {
        document.getElementById('modalCommentTitle').innerText = `COMMENTS · ${comments.length}`;

        document.getElementById('modalComments').innerHTML = comments.length
            ? comments.map((comment, index) => {
                const initials = getInitials(comment.creator);

                return `
                    <div class="task-comment">
                        <span class="avatar av-c${index % 6}" style="width:30px;height:30px;font-size:11px">${initials}</span>

                        <div class="body">
                            <div class="row" style="gap:8px">
                                <span class="who">${escapeHtml(comment.creator)}</span>
                                <span class="when">${escapeHtml(comment.created_at)}</span>
                            </div>

                            <div class="txt">${escapeHtml(comment.description)}</div>
                        </div>
                    </div>
                `;
            }).join('')
            : `<div style="font-size:13px;color:#94A3B8;text-align:center;padding:12px 0">
                    No comments yet.
               </div>`;
    }

    function storeTaskComment() {
        if (!activeTaskId) return;

        const input = document.getElementById('commentInput');
        const description = input.value.trim();

        if (!description) return;

        fetch(`/tasks/${activeTaskId}/comments`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ description })
        })
        .then(res => res.json())
        .then(data => {
            if (!data.status) return;

            const task = TASKS.find(item => Number(item.id) === Number(activeTaskId));

            if (task) {
                task.comments.push(data.comment);
                renderComments(task.comments);
            }

            input.value = '';
        })
        .catch(() => {
            alert('Comment add failed.');
        });
    }

    function updateTaskStatusFromModal() {
        if (!activeTaskId) return;

        const status = document.getElementById('modalTaskStatus').value;

        fetch(`/tasks/${activeTaskId}/status`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ status })
        })
        .then(res => res.json())
        .then(data => {
            if (!data.status) return;
            location.reload();
        })
        .catch(() => {
            alert('Task status update failed.');
        });
    }

    function hideTaskModal() {
        document.getElementById('taskModalRoot').style.display = 'none';
        activeTaskId = null;
    }

    function closeTaskModal(event) {
        if (event.target.classList.contains('task-modal-overlay')) {
            hideTaskModal();
        }
    }

    function normalizeTaskStatus(status) {
        const value = String(status || '').toLowerCase().replaceAll('_', ' ');

        if (value === 'done' || value === 'completed') return 'Done';
        if (value === 'in progress') return 'In Progress';

        return 'To Do';
    }

    function getInitials(name) {
        return String(name || 'User')
            .split(' ')
            .filter(Boolean)
            .slice(0, 2)
            .map(part => part[0].toUpperCase())
            .join('') || 'U';
    }

    function escapeHtml(value) {
        return String(value || '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Enter' && event.target.id === 'commentInput') {
            event.preventDefault();
            storeTaskComment();
        }
    });
</script>
@endpush