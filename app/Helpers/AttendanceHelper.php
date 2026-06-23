<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\User;


if (! function_exists('attendance_find_record')) {
    function attendance_find_record(string $table, $id, string $column = 'id')
    {
        if (empty($id)) {
            return null;
        }

        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
            return null;
        }

        return DB::table($table)
            ->where($column, $id)
            ->first();
    }
}

if (! function_exists('attendance_pick_value')) {
    function attendance_pick_value($record, array $columns, $default = null)
    {
        if (! $record) {
            return $default;
        }

        foreach ($columns as $column) {
            if (isset($record->{$column}) && $record->{$column} !== null && $record->{$column} !== '') {
                return $record->{$column};
            }
        }

        return $default;
    }
}

if (! function_exists('attendance_today_count')) {
    function attendance_today_count(int $userId, string $type): int
    {
        if (! Schema::hasTable('attendances')) {
            return 0;
        }

        $userColumn = null;

        foreach (['user_id', 'employee_id', 'staff_id'] as $column) {
            if (Schema::hasColumn('attendances', $column)) {
                $userColumn = $column;
                break;
            }
        }

        if (! $userColumn) {
            return 0;
        }

        $query = DB::table('attendances')
            ->where($userColumn, $userId);

        if (Schema::hasColumn('attendances', 'attendance_date')) {
            $query->whereDate('attendance_date', today());
        } elseif (Schema::hasColumn('attendances', 'date')) {
            $query->whereDate('date', today());
        } elseif (Schema::hasColumn('attendances', 'created_at')) {
            $query->whereDate('created_at', today());
        }

        if ($type === 'login') {
            if (Schema::hasColumn('attendances', 'type')) {
                return (clone $query)
                    ->whereIn('type', ['login', 'check_in', 'checkin', 'in'])
                    ->count();
            }

            if (Schema::hasColumn('attendances', 'check_in')) {
                return (clone $query)->whereNotNull('check_in')->count();
            }

            if (Schema::hasColumn('attendances', 'check_in_at')) {
                return (clone $query)->whereNotNull('check_in_at')->count();
            }
        }

        if ($type === 'logout') {
            if (Schema::hasColumn('attendances', 'type')) {
                return (clone $query)
                    ->whereIn('type', ['logout', 'check_out', 'checkout', 'out'])
                    ->count();
            }

            if (Schema::hasColumn('attendances', 'check_out')) {
                return (clone $query)->whereNotNull('check_out')->count();
            }

            if (Schema::hasColumn('attendances', 'check_out_at')) {
                return (clone $query)->whereNotNull('check_out_at')->count();
            }
        }

        return 0;
    }
}

if (! function_exists('attendance_rules')) {
    function attendance_rules($employeeId = null): array
    {
        $data = employee_full_details($employeeId);

        if (! $data['status']) {
            return $data;
        }

        $employee = $data['employee'];
        $officeTime = $employee->officeTime;

        $openingTime = $officeTime?->opening_time ?? '09:00:00';
        $closingTime = $officeTime?->closing_time ?? '18:00:00';

        $today = Carbon::today();
        $now = Carbon::now();

        $startAt = Carbon::parse($today->format('Y-m-d') . ' ' . $openingTime);
        $endAt = Carbon::parse($today->format('Y-m-d') . ' ' . $closingTime);

        /*
        |--------------------------------------------------------------------------
        | Night shift handling
        |--------------------------------------------------------------------------
        | Example: opening_time 22:00:00 and closing_time 06:00:00
        */
        if ($endAt->lessThanOrEqualTo($startAt)) {
            $endAt->addDay();
        }

        $maxCheckIn = 3;
        $maxCheckOut = 3;

        $todayAttendances = $employee->todayAttendances;

        $todayCheckInCount = $todayAttendances
            ->whereNotNull('check_in_at')
            ->count();

        $todayCheckOutCount = $todayAttendances
            ->whereNotNull('check_out_at')
            ->count();

        $isWithinOfficeTime = $now->betweenIncluded($startAt, $endAt);

        $canCheckIn = $isWithinOfficeTime && $todayCheckInCount < $maxCheckIn;
        $canCheckOut = $isWithinOfficeTime && $todayCheckOutCount < $maxCheckOut;

        return [
            'status' => true,

            'employee' => $employee,

            'office_time' => [
                'record' => $officeTime,
                'opening_time' => $openingTime,
                'closing_time' => $closingTime,
                'start_at' => $startAt->format('Y-m-d H:i:s'),
                'end_at' => $endAt->format('Y-m-d H:i:s'),
                'current_time' => $now->format('Y-m-d H:i:s'),
                'is_within_office_time' => $isWithinOfficeTime,
            ],

            'limits' => [
                'max_check_in' => $maxCheckIn,
                'max_check_out' => $maxCheckOut,
            ],

            'today_counts' => [
                'check_in' => $todayCheckInCount,
                'check_out' => $todayCheckOutCount,
            ],

            'permissions' => [
                'can_check_in' => $canCheckIn,
                'can_check_out' => $canCheckOut,
            ],

            'messages' => [
                'check_in' => ! $isWithinOfficeTime
                    ? 'Check in is only allowed during office time.'
                    : ($canCheckIn ? 'You can check in.' : 'Today check in limit completed.'),

                'check_out' => ! $isWithinOfficeTime
                    ? 'Check out is only allowed during office time.'
                    : ($canCheckOut ? 'You can check out.' : 'Today check out limit completed.'),
            ],

            'full_details' => $data,
        ];
    }

    if (! function_exists('employee_auth_user')) {
        function employee_auth_user()
        {
            foreach (['employee', 'web'] as $guard) {
                try {
                    if (auth()->guard($guard)->check()) {
                        return auth()->guard($guard)->user();
                    }
                } catch (Throwable $e) {
                    continue;
                }
            }

            return auth()->user();
        }
    }

    if (! function_exists('employee_full_details')) {
        function employee_full_details($employeeId = null): array
        {
            $authEmployee = employee_auth_user();

            if (! $authEmployee && ! $employeeId) {
                return [
                    'status' => false,
                    'message' => 'Employee not authenticated.',
                    'employee' => null,
                ];
            }

            $id = $employeeId ?: $authEmployee->id;

            $employee = User::query()
                ->with([
                    
                    'company',
                    'branch',
                    'department',
                    'post',
                    'officeTime',
                    'supervisor',

                    'employeeAccount',
                    'employeeSalary',
                    'employeeLeaveTypes.leaveType',

                    'todayAttendances',
                    'attendances' => function ($query) {
                        $query->latest('attendance_date')
                            ->latest('created_at')
                            ->limit(20);
                    },

                    'assetAssignments.asset',
                    'assets',

                    'leaveRequests.leaveType' => function ($query) {
                        //
                    },

                    'timeLeaves' => function ($query) {
                        $query->latest();
                    },

                    'tadas' => function ($query) {
                        $query->latest();
                    },

                    'advanceSalaries' => function ($query) {
                        $query->latest();
                    },

                    'awards' => function ($query) {
                        $query->latest();
                    },

                    'payslips' => function ($query) {
                        $query->latest();
                    },

                    'teamMeetings' => function ($query) {
                        $query->latest('meeting_date');
                    },

                    'notices' => function ($query) {
                        $query->latest('notice_publish_date');
                    },
                ])
                ->withCount([
                    'attendances',
                    'leaveRequests',
                    'timeLeaves',
                    'tadas',
                    'advanceSalaries',
                    'awards',
                    'payslips',
                    'assetAssignments',
                    'teamMeetings',
                    'notices',
                ])
                ->find($id);

            if (! $employee) {
                return [
                    'status' => false,
                    'message' => 'Employee not found.',
                    'employee' => null,
                ];
            }

            return [
                'status' => true,
                'message' => 'Employee details loaded successfully.',

                'employee' => $employee,

                'basic' => [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'email' => $employee->email,
                    'work_email' => $employee->work_email,
                    'phone' => $employee->phone,
                    'employee_code' => $employee->employee_code,
                    'status' => $employee->status,
                    'is_active' => $employee->is_active,
                    'employment_type' => $employee->employment_type,
                    'user_type' => $employee->user_type,
                    'joining_date' => $employee->joining_date,
                ],

                'company_details' => [
                    
                    'company' => $employee->company,
                    'branch' => $employee->branch,
                    'department' => $employee->department,
                    'post' => $employee->post,
                    
                    'supervisor' => $employee->supervisor,
                    'office_time' => $employee->officeTime,
                ],

                'salary_details' => [
                    'account' => $employee->employeeAccount,
                    'salary' => $employee->employeeSalary,
                    'payslips' => $employee->payslips,
                ],

                'leave_details' => [
                    'leave_types' => $employee->employeeLeaveTypes,
                    'leave_requests' => $employee->leaveRequests,
                    'time_leaves' => $employee->timeLeaves,
                ],

                'attendance_details' => [
                    'today' => $employee->todayAttendances,
                    'recent' => $employee->attendances,
                ],

                'asset_details' => [
                    'assignments' => $employee->assetAssignments,
                    'assets' => $employee->assets,
                ],

                'other_details' => [
                    'tadas' => $employee->tadas,
                    'advance_salaries' => $employee->advanceSalaries,
                    'awards' => $employee->awards,
                    'team_meetings' => $employee->teamMeetings,
                    'notices' => $employee->notices,
                ],

                'counts' => [
                    'attendances' => $employee->attendances_count,
                    'leave_requests' => $employee->leave_requests_count,
                    'time_leaves' => $employee->time_leaves_count,
                    'tadas' => $employee->tadas_count,
                    'advance_salaries' => $employee->advance_salaries_count,
                    'awards' => $employee->awards_count,
                    'payslips' => $employee->payslips_count,
                    'assets' => $employee->asset_assignments_count,
                    'team_meetings' => $employee->team_meetings_count,
                    'notices' => $employee->notices_count,
                ],
            ];
        }
    }
}
