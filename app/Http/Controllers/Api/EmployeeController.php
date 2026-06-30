<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Employee\Concerns\WorksWithEmployee;
use App\Http\Requests\Employee\StoreLeaveRequest;
use App\Http\Requests\Employee\StoreResignationRequest;
use App\Http\Requests\Employee\StoreTadaRequest;
use App\Http\Requests\Employee\StoreTimeLeaveRequest;
use App\Http\Requests\Employee\UpdateProfileRequest;
use App\Models\AssetAssignment;
use App\Models\Attendance;
use App\Models\GeneratedPayroll;
use App\Models\Holiday;
use App\Models\LeaveType;
use App\Models\Notice;
use App\Models\Notification;
use App\Models\Project;
use App\Models\Resignation;
use App\Models\Tada;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\TeamMeeting;
use App\Models\Termination;
use App\Models\Transfer;
use App\Models\User;
use App\Models\UserNotification;
use App\Services\Employee\EmployeeLeaveService;
use App\Support\SharedTableId;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    use WorksWithEmployee;

    public function __construct(private readonly EmployeeLeaveService $employeeLeaveService) {}

    public function dashboard(): JsonResponse
    {
        $employee = $this->employee()->load([
            'company',
            'branch',
            'department',
            'post',
            'officeTime',
            'todayAttendances',
            'leaveRequests.leaveType',
            'timeLeaves',
            'employeeLeaveTypes.leaveType',
            'assetAssignments.asset',
            'teamMeetings',
            'notices',
        ]);

        $attendanceRules = function_exists('attendance_rules')
            ? attendance_rules($employee->id, $employee)
            : [];

        return response()->json([
            'status' => true,
            'message' => 'Dashboard loaded successfully.',
            'employee' => $employee,
            'attendance_rules' => $attendanceRules,
            'summary' => [
                'today_attendances' => $employee->todayAttendances,
                'leave' => [
                    'pending' => $employee->leaveRequests->where('status', 'pending')->count(),
                    'approved' => $employee->leaveRequests->where('status', 'approved')->count(),
                    'rejected' => $employee->leaveRequests->where('status', 'rejected')->count(),
                    'types' => $employee->employeeLeaveTypes->count(),
                ],
                'assets' => [
                    'total' => $employee->assetAssignments->count(),
                    'assigned' => $employee->assetAssignments->where('status', 'assigned')->count(),
                    'returned' => $employee->assetAssignments->where('status', 'returned')->count(),
                ],
                'meetings' => $employee->teamMeetings->count(),
                'notices' => $employee->notices->count(),
            ],
        ]);
    }

    public function attendance(): JsonResponse
    {
        $employee = $this->employee();

        return response()->json([
            'status' => true,
            'attendance_rules' => function_exists('attendance_rules') ? attendance_rules($employee->id, $employee) : [],
            'attendances' => $employee->attendances()
                ->with('officeTime')
                ->latest('attendance_date')
                ->latest('created_at')
                ->paginate(20),
            'today' => $this->attendanceState(),
        ]);
    }

    public function attendanceStatus(): JsonResponse
    {
        return response()->json([
            'status' => true,
            'attendance' => $this->attendanceState(),
        ]);
    }

    public function checkIn(): JsonResponse
    {
        $employee = $this->employee();
        $openAttendance = Attendance::query()
            ->where('user_id', $employee->id)
            ->whereDate('attendance_date', today())
            ->whereNotNull('check_in_at')
            ->whereNull('check_out_at')
            ->first();

        if ($openAttendance) {
            return response()->json([
                'status' => false,
                'message' => 'You are already checked in for today.',
                'attendance' => $this->attendanceState(),
            ], 422);
        }

        $todayCheckInCount = Attendance::query()
            ->where('user_id', $employee->id)
            ->whereDate('attendance_date', today())
            ->whereNotNull('check_in_at')
            ->count();

        if ($todayCheckInCount >= 3) {
            return response()->json([
                'status' => false,
                'message' => 'Maximum 3 check-ins are allowed for today.',
                'attendance' => $this->attendanceState(),
            ], 422);
        }

        DB::transaction(function () use ($employee): void {
            Attendance::query()->create([
                'id' => SharedTableId::next(Attendance::class),
                'user_id' => $employee->id,
                'company_id' => $employee->company_id,
                'attendance_date' => today(),
                'check_in_at' => now()->format('H:i:s'),
                'attendance_status' => true,
                'created_by' => $employee->id,
                'check_in_type' => 'api',
                'check_out_type' => 'api',
                'office_time_id' => $employee->office_time_id,
            ]);
        });

        return response()->json([
            'status' => true,
            'message' => 'Checked in successfully.',
            'attendance' => $this->attendanceState(),
        ]);
    }

    public function checkOut(): JsonResponse
    {
        $employee = $this->employee();
        $attendance = Attendance::query()
            ->where('user_id', $employee->id)
            ->whereDate('attendance_date', today())
            ->whereNotNull('check_in_at')
            ->whereNull('check_out_at')
            ->latest('created_at')
            ->first();

        if (! $attendance) {
            return response()->json([
                'status' => false,
                'message' => 'No active check-in found for today.',
                'attendance' => $this->attendanceState(),
            ], 422);
        }

        $attendanceDate = $attendance->attendance_date instanceof Carbon
            ? $attendance->attendance_date->toDateString()
            : Carbon::parse($attendance->attendance_date)->toDateString();

        $workedSeconds = Carbon::parse($attendanceDate.' '.$attendance->check_in_at)->diffInSeconds(now());

        $attendance->update([
            'check_out_at' => now()->format('H:i:s'),
            'worked_hour' => $workedSeconds,
            'updated_by' => $employee->id,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Checked out successfully.',
            'attendance' => $this->attendanceState(),
        ]);
    }

    public function leaves(): JsonResponse
    {
        $employee = $this->employee();
        $gender = $employee->gender;
        $leaveTypes = LeaveType::query()
            ->where('branch_id', $employee->branch_id)
            ->where('is_active', 1)
            ->where(fn ($query) => $query->where('gender', $gender)->orWhere('gender', 'all'))
            ->orderBy('id')
            ->get();

        return response()->json([
            'status' => true,
            'leave_types' => $leaveTypes,
            'leave_requests' => $employee->leaveRequests()->with('leaveType')->latest('leave_requested_date')->paginate(15),
            'time_leaves' => $employee->timeLeaves()->latest('issue_date')->paginate(15),
        ]);
    }

    public function storeLeave(StoreLeaveRequest $request): JsonResponse
    {
        try {
            $leave = $this->employeeLeaveService->storeFullLeave($request->validated(), $this->employee());

            return response()->json([
                'status' => true,
                'message' => 'Leave request submitted.',
                'leave' => $leave->load('leaveType'),
            ], 201);
        } catch (Exception $exception) {
            return response()->json([
                'status' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }
    }

    public function storeTimeLeave(StoreTimeLeaveRequest $request): JsonResponse
    {
        try {
            $leave = $this->employeeLeaveService->storeShortLeave($request->validated(), $this->employee());

            return response()->json([
                'status' => true,
                'message' => 'Time leave request submitted.',
                'time_leave' => $leave,
            ], 201);
        } catch (Exception $exception) {
            return response()->json([
                'status' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }
    }

    public function tada(): JsonResponse
    {
        $employee = $this->employee();
        $allTadas = Tada::query()->where('employee_id', $employee->id)->get();

        return response()->json([
            'status' => true,
            'stats' => [
                'total_claimed' => $allTadas->sum('total_expense'),
                'approved' => $allTadas->filter(fn (Tada $tada): bool => in_array(strtolower((string) $tada->status), ['approved', 'accepted'], true))->sum('total_expense'),
                'pending' => $allTadas->filter(fn (Tada $tada): bool => strtolower((string) $tada->status) === 'pending')->sum('total_expense'),
                'paid' => $allTadas->where('is_settled', true)->sum('total_expense'),
            ],
            'claims' => Tada::query()->where('employee_id', $employee->id)->latest()->paginate(15),
        ]);
    }

    public function storeTada(StoreTadaRequest $request): JsonResponse
    {
        $employee = $this->employee();
        $validated = $request->validated();

        $tada = DB::transaction(fn () => Tada::query()->create([
            'id' => SharedTableId::next(Tada::class),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'total_expense' => $validated['total_expense'],
            'status' => 'pending',
            'is_active' => true,
            'is_settled' => false,
            'employee_id' => $employee->id,
            'created_by' => $employee->id,
            'branch_id' => $employee->branch_id,
            'department_id' => $employee->department_id,
        ]));

        return response()->json([
            'status' => true,
            'message' => 'TADA claim submitted.',
            'claim' => $tada,
        ], 201);
    }

    public function payroll(): JsonResponse
    {
        $employee = $this->employee();
        $allPayrolls = GeneratedPayroll::query()->where('employee_id', $employee->id)->get();

        return response()->json([
            'status' => true,
            'stats' => [
                'records' => $allPayrolls->count(),
                'base_salary' => $allPayrolls->sum('base_salary'),
                'net_salary' => $allPayrolls->sum('net_salary'),
                'overtime_pay' => $allPayrolls->sum('overtime_pay'),
                'deductions' => $allPayrolls->sum('undertime_deduction') + $allPayrolls->sum('unpaid_leave_deduction') + $allPayrolls->sum('tax'),
                'tada_amount' => $allPayrolls->sum('tada_amount'),
            ],
            'payrolls' => GeneratedPayroll::query()->where('employee_id', $employee->id)->latest()->paginate(15),
        ]);
    }

    public function resignations(): JsonResponse
    {
        $employee = $this->employee();

        return response()->json([
            'status' => true,
            'resignations' => Resignation::query()->where('employee_id', $employee->id)->latest('resignation_date')->get(),
            'transfers' => Transfer::query()->where('employee_id', $employee->id)->latest('transfer_date')->limit(10)->get(),
            'terminations' => Termination::query()->where('employee_id', $employee->id)->latest('termination_date')->limit(10)->get(),
        ]);
    }

    public function storeResignation(StoreResignationRequest $request): JsonResponse
    {
        $employee = $this->employee();
        $validated = $request->validated();
        $documentPath = $request->file('document')?->store('resignation-documents', 'public');

        $resignation = DB::transaction(fn () => Resignation::query()->create([
            'id' => SharedTableId::next(Resignation::class),
            'employee_id' => $employee->id,
            'resignation_date' => $validated['resignation_date'],
            'last_working_day' => $validated['last_working_day'],
            'reason' => $validated['reason'],
            'document' => $documentPath,
            'status' => 'pending',
            'created_by' => $employee->id,
            'branch_id' => $employee->branch_id,
            'department_id' => $employee->department_id,
        ]));

        return response()->json([
            'status' => true,
            'message' => 'Resignation request submitted.',
            'resignation' => $resignation,
        ], 201);
    }

    public function team(): JsonResponse
    {
        $employee = $this->employee();

        return response()->json([
            'status' => true,
            'members' => User::query()
                ->with(['branch', 'department', 'post', 'supervisor'])
                ->where('company_id', $employee->company_id)
                ->where('is_active', true)
                ->whereNull('deleted_at')
                ->orderByRaw('department_id = ? desc', [$employee->department_id])
                ->orderBy('name')
                ->paginate(24),
        ]);
    }

    public function assets(): JsonResponse
    {
        $employee = $this->employee();
        $assignmentsQuery = AssetAssignment::query()
            ->with(['asset.branch', 'asset.type.branch', 'branch', 'department'])
            ->where('user_id', $employee->id);
        $allAssignments = (clone $assignmentsQuery)->get();

        return response()->json([
            'status' => true,
            'stats' => [
                'records' => $allAssignments->count(),
                'currently_assigned' => $allAssignments->reject(fn (AssetAssignment $assignment): bool => strtolower((string) $assignment->status) === 'returned')->count(),
                'returned' => $allAssignments->filter(fn (AssetAssignment $assignment): bool => strtolower((string) $assignment->status) === 'returned')->count(),
                'return_pending' => $allAssignments->filter(fn (AssetAssignment $assignment): bool => str_contains(strtolower(str_replace('_', ' ', (string) $assignment->status)), 'return pending'))->count(),
            ],
            'assignments' => $assignmentsQuery->latest('assigned_date')->paginate(15),
        ]);
    }

    public function requestAssetReturn(Request $request, AssetAssignment $assetAssignment): JsonResponse
    {
        $employee = $this->employee();
        abort_unless((int) $assetAssignment->user_id === (int) $employee->id, 404);

        $status = strtolower((string) $assetAssignment->status);

        if ($status === 'returned') {
            return response()->json(['status' => false, 'message' => 'This asset has already been returned.'], 422);
        }

        if (str_contains(strtolower(str_replace('_', ' ', $status)), 'return pending')) {
            return response()->json(['status' => false, 'message' => 'Your return request is already waiting for admin approval.'], 422);
        }

        $validated = $request->validate([
            'return_condition' => ['required', Rule::in(['working', 'non_working', 'maintenance'])],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $assetAssignment->forceFill([
            'status' => 'return_pending',
            'return_condition' => $validated['return_condition'],
            'notes' => $validated['notes'] ?? null,
            'returned_date' => null,
        ])->save();

        return response()->json([
            'status' => true,
            'message' => 'Asset return request sent for admin approval.',
            'assignment' => $assetAssignment->fresh(['asset.branch', 'asset.type.branch', 'branch', 'department']),
        ]);
    }

    public function projects(): JsonResponse
    {
        $employee = $this->employee();

        return response()->json([
            'status' => true,
            'projects' => $this->visibleProjectsQuery($employee)
                ->with(['members:id,name', 'leaders:id,name'])
                ->withCount('tasks')
                ->latest('start_date')
                ->paginate(12),
            'tasks' => $this->visibleTasksQuery($employee)->with('project:id,name')->latest('end_date')->limit(12)->get(),
        ]);
    }

    public function project(Project $project): JsonResponse
    {
        $employee = $this->employee();
        abort_unless($this->visibleProjectsQuery($employee)->whereKey($project->id)->exists(), 403);

        $project->load(['members:id,name', 'leaders:id,name']);
        $tasks = $this->visibleTasksQuery($employee)
            ->where('project_id', $project->id)
            ->with(['assignees:id,name', 'checklists.assignee:id,name', 'comments.creator:id,name'])
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'project' => $project,
            'tasks' => $tasks,
        ]);
    }

    public function toggleTask(Task $task): JsonResponse
    {
        $employee = $this->employee();
        abort_unless($this->visibleTasksQuery($employee)->whereKey($task->id)->exists(), 403);

        $task->update([
            'status' => in_array($task->status, $this->doneStatuses(), true) ? 'To Do' : 'Done',
        ]);

        return response()->json(['status' => true, 'task_status' => $task->status]);
    }

    public function updateTaskStatus(Request $request, Task $task): JsonResponse
    {
        $employee = $this->employee();
        abort_unless($this->visibleTasksQuery($employee)->whereKey($task->id)->exists(), 403);

        $validated = $request->validate(['status' => ['required', 'string', 'in:To Do,In Progress,Done']]);
        $task->update(['status' => $validated['status']]);

        return response()->json(['status' => true, 'task_status' => $task->status]);
    }

    public function storeTaskComment(Request $request, Task $task): JsonResponse
    {
        $employee = $this->employee();
        abort_unless($this->visibleTasksQuery($employee)->whereKey($task->id)->exists(), 403);

        $validated = $request->validate(['description' => ['required', 'string', 'max:2000']]);
        $comment = TaskComment::query()->create([
            'task_id' => $task->id,
            'description' => $validated['description'],
            'created_by' => $employee->id,
        ])->load('creator:id,name');

        return response()->json(['status' => true, 'comment' => $comment], 201);
    }

    public function holidays(): JsonResponse
    {
        $employee = $this->employee();

        return response()->json([
            'status' => true,
            'next_holiday' => Holiday::query()->where('company_id', $employee->company_id)->where('is_active', true)->whereDate('event_date', '>=', today())->orderBy('event_date')->first(),
            'holidays' => Holiday::query()->where('company_id', $employee->company_id)->where('is_active', true)->orderBy('event_date')->paginate(20),
        ]);
    }

    public function notices(): JsonResponse
    {
        $employee = $this->employee();
        $noticesQuery = Notice::query()
            ->with(['branch', 'company', 'creator:id,name,email', 'updater:id,name,email', 'receivers:id,name,email,work_email,avatar,branch_id,department_id'])
            ->where('company_id', $employee->company_id)
            ->where('is_active', true)
            ->whereHas('receivers', fn (Builder $query) => $query->whereKey($employee->id));

        return response()->json([
            'status' => true,
            'notices' => $noticesQuery->orderByDesc('notice_publish_date')->orderByDesc('created_at')->paginate(15),
        ]);
    }

    public function meetings(): JsonResponse
    {
        $employee = $this->employee();

        $meetingsQuery = TeamMeeting::query()
            ->with(['branch', 'company', 'creator:id,name,email', 'departments:id,dept_name,branch_id', 'members:id,name,email,work_email,avatar,department_id,branch_id'])
            ->where('company_id', $employee->company_id)
            ->where(function (Builder $query) use ($employee): void {
                $query->whereHas('members', fn (Builder $memberQuery) => $memberQuery->whereKey($employee->id));

                if ($employee->department_id) {
                    $query->orWhereHas('departments', fn (Builder $departmentQuery) => $departmentQuery->whereKey($employee->department_id));
                }
            });

        return response()->json([
            'status' => true,
            'meetings' => $meetingsQuery->orderByDesc('meeting_date')->orderByDesc('meeting_start_time')->paginate(15),
        ]);
    }

    public function inbox(): JsonResponse
    {
        $employee = $this->employee();

        return response()->json([
            'status' => true,
            'user_notifications' => UserNotification::query()->with('notification')->where('user_id', $employee->id)->latest()->paginate(15),
            'general_notifications' => Notification::query()->where('company_id', $employee->company_id)->where('is_active', true)->latest('notification_publish_date')->limit(8)->get(),
        ]);
    }

    public function profile(): JsonResponse
    {
        return response()->json([
            'status' => true,
            'employee' => $this->employee()->load(['company', 'branch', 'department', 'post', 'officeTime', 'supervisor', 'employeeAccount', 'employeeSalary']),
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $employee = $this->employee();
        $validated = $request->validated();
        $profileData = Arr::except($validated, ['avatar']);

        if ($request->hasFile('avatar')) {
            $oldAvatar = (string) $employee->avatar;
            $extension = $request->file('avatar')->getClientOriginalExtension();
            $fileName = 'employee-'.$employee->id.'-'.Str::uuid().'.'.$extension;

            Storage::disk('public')->makeDirectory('profile-avatars');
            $profileData['avatar'] = $request->file('avatar')->storeAs('profile-avatars', $fileName, 'public');

            if (str_starts_with($oldAvatar, 'profile-avatars/')) {
                Storage::disk('public')->delete($oldAvatar);
            }
        }

        $employee->forceFill($profileData)->save();

        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully.',
            'employee' => $employee->fresh(['company', 'branch', 'department', 'post']),
        ]);
    }

    private function attendanceState(): array
    {
        $employee = $this->employee();
        $attendances = Attendance::query()
            ->where('user_id', $employee->id)
            ->whereDate('attendance_date', today())
            ->orderBy('created_at')
            ->get();

        return [
            'has_open_attendance' => $attendances->whereNotNull('check_in_at')->whereNull('check_out_at')->isNotEmpty(),
            'sessions' => $attendances,
        ];
    }

    private function visibleProjectsQuery(User $employee): Builder
    {
        return Project::query()
            ->where(fn (Builder $query) => $query
                ->whereIn('id', $this->assignedIds($employee, 'project'))
                ->orWhereHas('leaders', fn (Builder $leaderQuery) => $leaderQuery->whereKey($employee->id))
                ->orWhere('branch_id', $employee->branch_id)
                ->when($employee->department_id, fn (Builder $query) => $query->orWhere('department_ids', 'like', "%{$employee->department_id}%")));
    }

    private function visibleTasksQuery(User $employee): Builder
    {
        return Task::query()->whereIn('project_id', $this->visibleProjectsQuery($employee)->select('id'));
    }

    private function doneStatuses(): array
    {
        return ['done', 'Done', 'completed', 'Completed'];
    }
}
