<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Employee\Concerns\WorksWithEmployee;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class ProjectController extends Controller
{
    use WorksWithEmployee;

    public function index(): View
    {
        $employee = $this->employee();
        $projectIds = $this->assignedIds($employee, 'project');

        return view('projects.index', [
            'employee' => $employee,
            'projects' => Project::query()
                ->withCount('tasks')
                ->where(function (Builder $query) use ($employee, $projectIds): void {
                    $query->whereIn('id', $projectIds)
                        ->orWhereHas('leaders', fn (Builder $leaderQuery) => $leaderQuery->whereKey($employee->id))
                        ->orWhere('branch_id', $employee->branch_id);

                    if ($employee->department_id) {
                        $query->orWhere('department_ids', 'like', '%'.$employee->department_id.'%');
                    }
                })
                ->latest('start_date')
                ->paginate(12),
            'tasks' => Task::query()
                ->with('project')
                ->where(function (Builder $query) use ($employee): void {
                    $query->whereIn('id', $this->assignedIds($employee, 'task'))
                        ->orWhereHas('checklists', fn (Builder $checklistQuery) => $checklistQuery->where('assigned_to', $employee->id));
                })
                ->latest('end_date')
                ->limit(12)
                ->get(),
        ]);
    }
}
