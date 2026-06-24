<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name',
    'email',
    'work_email',
    'username',
    'password',
    'phone',
    'address',
    'avatar',
    'status',
    'is_active',
    'company_id',
    'branch_id',
    'department_id',
    'post_id',
    'supervisor_id',
    'office_time_id',
    'remember_token',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    public function scopeActiveEmployee(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where('status', 'verified');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'dob' => 'date',
            'email_verified_at' => 'datetime',
            'joining_date' => 'date',
            'contract_start_date' => 'date',
            'contract_end_date' => 'date',
            'is_active' => 'boolean',
            'online_status' => 'boolean',
            'logout_status' => 'boolean',
            'allow_holiday_check_in' => 'boolean',
            'password' => 'hashed',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Company / Employee Basic Relations
    |--------------------------------------------------------------------------
    */

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    public function officeTime()
    {
        return $this->belongsTo(OfficeTime::class, 'office_time_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function subordinates()
    {
        return $this->hasMany(User::class, 'supervisor_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Attendance Relations
    |--------------------------------------------------------------------------
    */

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'user_id');
    }

    public function todayAttendances()
    {
        return $this->hasMany(Attendance::class, 'user_id')
            ->whereDate('attendance_date', today());
    }

    public function latestAttendance()
    {
        return $this->hasOne(Attendance::class, 'user_id')
            ->latestOfMany();
    }

    /*
    |--------------------------------------------------------------------------
    | Salary / Account Relations
    |--------------------------------------------------------------------------
    */

    public function employeeAccount()
    {
        return $this->hasOne(EmployeeAccount::class, 'user_id');
    }

    public function employeeSalary()
    {
        return $this->hasOne(EmployeeSalary::class, 'employee_id');
    }

    public function advanceSalaries()
    {
        return $this->hasMany(AdvanceSalary::class, 'employee_id');
    }

    public function payslips()
    {
        return $this->hasMany(EmployeePayslip::class, 'employee_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Leave Relations
    |--------------------------------------------------------------------------
    */

    public function employeeLeaveTypes()
    {
        return $this->hasMany(EmployeeLeaveType::class, 'employee_id');
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequestMaster::class, 'requested_by');
    }

    public function timeLeaves()
    {
        return $this->hasMany(TimeLeave::class, 'requested_by');
    }

    /*
    |--------------------------------------------------------------------------
    | TADA / Awards
    |--------------------------------------------------------------------------
    */

    public function tadas()
    {
        return $this->hasMany(Tada::class, 'employee_id');
    }

    public function awards()
    {
        return $this->hasMany(Award::class, 'employee_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Asset Relations
    |--------------------------------------------------------------------------
    */

    public function assetAssignments()
    {
        return $this->hasMany(AssetAssignment::class, 'user_id');
    }

    public function assets()
    {
        return $this->belongsToMany(
            Asset::class,
            'asset_assignments',
            'user_id',
            'asset_id'
        )->withPivot([
            'status',
            'assigned_date',
            'returned_date',
            'return_condition',
            'notes',
            'branch_id',
            'department_id',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Team Meeting / Notice Relations
    |--------------------------------------------------------------------------
    */

    public function teamMeetings()
    {
        return $this->belongsToMany(
            TeamMeeting::class,
            'team_meeting_members',
            'meeting_participator_id',
            'team_meeting_id'
        );
    }

    public function notices()
    {
        return $this->belongsToMany(
            Notice::class,
            'notice_receivers',
            'notice_receiver_id',
            'notice_id'
        );
    }
}
