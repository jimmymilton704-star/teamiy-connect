<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Employee\Concerns\WorksWithEmployee;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskComment;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    use WorksWithEmployee;

    public function index(): View
    {
        $employee = $this->employee();

        $projects = $this->visibleProjectsQuery($employee)
            ->with([
                'members:id,name',
                'leaders:id,name',
            ])
            ->withCount([
                'tasks',
                'tasks as tasks_done_count' => fn ($query) => $query
                    ->whereIn('status', $this->doneStatuses()),
            ])
            ->latest('start_date')
            ->paginate(12);

        $tasks = $this->visibleTasksQuery($employee)
            ->with('project:id,name')
            ->latest('end_date')
            ->limit(12)
            ->get();

        return view('projects.index', compact('employee', 'projects', 'tasks'));
    }

    public function show(Project $project): View
    {
        $employee = $this->employee();

        abort_unless(
            $this->visibleProjectsQuery($employee)->whereKey($project->id)->exists(),
            403
        );

        $project->load([
            'members:id,name',
            'leaders:id,name',
        ]);

        $tasks = $this->visibleTasksQuery($employee)
            ->where('project_id', $project->id)
            ->with([
                'assignees:id,name',
                'checklists.assignee:id,name',
                'comments.creator:id,name',
            ])
            ->latest()
            ->get();

        $project->setRelation('tasks', $tasks);

        $totalTasks = $tasks->count();

        $doneTasks = $tasks
            ->whereIn('status', $this->doneStatuses())
            ->count();

        $todoTasks = $tasks
            ->whereIn('status', ['todo', 'To Do', 'pending', 'Pending'])
            ->count();

        $progressTasks = $tasks
            ->whereIn('status', ['in_progress', 'In Progress'])
            ->count();

        $progress = $totalTasks > 0
            ? round(($doneTasks / $totalTasks) * 100)
            : 0;

        return view('projects.show', compact(
            'project',
            'totalTasks',
            'doneTasks',
            'todoTasks',
            'progressTasks',
            'progress'
        ));
    }

    public function toggleTaskStatus(Task $task): JsonResponse
    {
        $employee = $this->employee();

        abort_unless(
            $this->visibleTasksQuery($employee)->whereKey($task->id)->exists(),
            403
        );

        $isDone = in_array($task->status, $this->doneStatuses(), true);

        $task->update([
            'status' => $isDone ? 'To Do' : 'Done',
        ]);

        return response()->json([
            'status' => true,
            'task_status' => $task->status,
        ]);
    }

    public function updateTaskStatus(Request $request, Task $task): JsonResponse
    {
        $employee = $this->employee();

        abort_unless(
            $this->visibleTasksQuery($employee)->whereKey($task->id)->exists(),
            403
        );

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:To Do,In Progress,Done'],
        ]);

        $task->update([
            'status' => $validated['status'],
        ]);

        return response()->json([
            'status' => true,
            'task_status' => $task->status,
        ]);
    }

    public function storeTaskComment(Request $request, Task $task): JsonResponse
    {
        $employee = $this->employee();

        abort_unless(
            $this->visibleTasksQuery($employee)->whereKey($task->id)->exists(),
            403
        );

        $validated = $request->validate([
            'description' => ['required', 'string', 'max:2000'],
        ]);

        $comment = TaskComment::create([
            'task_id' => $task->id,
            'description' => $validated['description'],
            'created_by' => auth()->id(),
        ]);

        $comment->load('creator:id,name');

        return response()->json([
            'status' => true,
            'comment' => [
                'id' => $comment->id,
                'description' => $comment->description,
                'creator' => $comment->creator->name ?? 'User',
                'created_at' => $comment->created_at?->diffForHumans() ?? 'Just now',
            ],
        ]);
    }

    private function visibleProjectsQuery($employee): Builder
    {
        return Project::query()
            ->where(fn (Builder $query) => $query
                ->whereIn('id', $this->assignedIds($employee, 'project'))
                ->orWhereHas('leaders', fn (Builder $leaderQuery) => $leaderQuery
                    ->whereKey($employee->id)
                )
                ->orWhere('branch_id', $employee->branch_id)
                ->when(
                    $employee->department_id,
                    fn (Builder $query) => $query
                        ->orWhere('department_ids', 'like', "%{$employee->department_id}%")
                )
            );
    }

    private function visibleTasksQuery($employee): Builder
    {
        return Task::query()
            ->whereIn('project_id', $this->visibleProjectsQuery($employee)->select('id'));
    }

    private function doneStatuses(): array
    {
        return ['done', 'Done', 'completed', 'Completed'];
    }
}