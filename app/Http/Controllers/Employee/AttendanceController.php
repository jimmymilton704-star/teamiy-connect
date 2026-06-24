<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Employee\Concerns\WorksWithEmployee;
use App\Models\Attendance;
use App\Support\SharedTableId;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    use WorksWithEmployee;

    public function index(): View
    {
        $employee = $this->employee();

        return view('attendance.index', [
            'employee' => $employee,
            'attendanceRules' => function_exists('attendance_rules') ? attendance_rules() : [],
            'attendances' => $employee->attendances()
                ->with('officeTime')
                ->latest('attendance_date')
                ->latest('created_at')
                ->paginate(20),
        ]);
    }

    public function status(): JsonResponse
    {
        return response()->json($this->attendanceState());
    }

    public function checkIn(Request $request): RedirectResponse|JsonResponse
    {
        $employee = $this->employee();

        $openAttendance = Attendance::query()
            ->where('user_id', $employee->id)
            ->whereDate('attendance_date', today())
            ->whereNotNull('check_in_at')
            ->whereNull('check_out_at')
            ->first();

        if ($openAttendance) {
            if ($request->expectsJson()) {
                return response()->json($this->attendanceState('You are already checked in for today.'));
            }

            return back()->with('status', 'You are already checked in for today.');
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
                'check_in_type' => 'web',
                'check_out_type' => 'web',
                'office_time_id' => $employee->office_time_id,
            ]);
        });

        if ($request->expectsJson()) {
            return response()->json($this->attendanceState('Checked in successfully.'));
        }

        return back()->with('status', 'Checked in successfully.');
    }

    public function checkOut(Request $request): RedirectResponse|JsonResponse
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
            if ($request->expectsJson()) {
                return response()->json($this->attendanceState('No active check-in found for today.'));
            }

            return back()->with('status', 'No active check-in found for today.');
        }

        $checkIn = Carbon::parse($attendance->attendance_date->toDateString().' '.$attendance->check_in_at);
        $workedHours = round($checkIn->diffInMinutes(now()) / 60, 2);

        $attendance->update([
            'check_out_at' => now()->format('H:i:s'),
            'worked_hour' => $workedHours,
            'updated_by' => $employee->id,
        ]);

        if ($request->expectsJson()) {
            return response()->json($this->attendanceState('Checked out successfully.'));
        }

        return back()->with('status', 'Checked out successfully.');
    }

    private function attendanceState(?string $message = null): array
    {
        $employee = $this->employee();

        $attendances = Attendance::query()
            ->where('user_id', $employee->id)
            ->whereDate('attendance_date', today())
            ->orderBy('created_at')
            ->get();

        $hasOpenAttendance = $attendances
            ->whereNotNull('check_in_at')
            ->whereNull('check_out_at')
            ->isNotEmpty();

        return [
            'message' => $message,
            'has_open_attendance' => $hasOpenAttendance,
            'action_url' => $hasOpenAttendance
                ? route('attendance.check-out')
                : route('attendance.check-in'),
            'action_label' => $hasOpenAttendance ? 'Check Out' : 'Check In',
            'sessions' => $attendances->map(fn (Attendance $attendance): array => $this->attendanceSession($attendance))->values(),
        ];
    }

    private function attendanceSession(Attendance $attendance): array
    {
        $checkIn = $attendance->check_in_at
            ? Carbon::parse($attendance->attendance_date->toDateString().' '.$attendance->check_in_at)
            : null;

        $checkOut = $attendance->check_out_at
            ? Carbon::parse($attendance->attendance_date->toDateString().' '.$attendance->check_out_at)
            : null;

        return [
            'date' => $attendance->attendance_date?->format('Y-m-d') ?? today()->format('Y-m-d'),
            'inTime' => $checkIn?->format('h:i:s A') ?? '-',
            'outTime' => $checkOut?->format('h:i:s A') ?? '',
            'inEpochMs' => $checkIn ? $checkIn->timestamp * 1000 : 0,
            'outEpochMs' => $checkOut ? $checkOut->timestamp * 1000 : null,
            'durationMs' => ($checkIn && $checkOut) ? $checkOut->diffInMilliseconds($checkIn) : 0,
        ];
    }
}
