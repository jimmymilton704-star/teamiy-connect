<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Employee\Concerns\WorksWithEmployee;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

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

        $tasks = Task::query()
            ->with('project:id,name')
            ->where(fn (Builder $query) => $query
                ->whereIn('id', $this->assignedIds($employee, 'task'))
                ->orWhereHas('checklists', fn (Builder $checklistQuery) => $checklistQuery
                    ->where('assigned_to', $employee->id)
                )
            )
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
            'tasks' => fn ($query) => $query->latest(),
        ]);

        $totalTasks = $project->tasks->count();

        $doneTasks = $project->tasks
            ->whereIn('status', $this->doneStatuses())
            ->count();

        $todoTasks = $project->tasks
            ->whereIn('status', ['todo', 'To Do', 'pending', 'Pending'])
            ->count();

        $progressTasks = $project->tasks
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
            $this->visibleProjectsQuery($employee)->whereKey($task->project_id)->exists(),
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

    private function visibleProjectsQuery($employee): Builder
    {
        $projectIds = $this->assignedIds($employee, 'project');

        return Project::query()
            ->where(fn (Builder $query) => $query
                ->whereIn('id', $projectIds)
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

    private function doneStatuses(): array
    {
        return ['done', 'Done', 'completed', 'Completed'];
    }
}