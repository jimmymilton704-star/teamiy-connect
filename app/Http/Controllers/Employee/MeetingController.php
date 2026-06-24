<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Employee\Concerns\WorksWithEmployee;
use App\Models\TeamMeeting;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class MeetingController extends Controller
{
    use WorksWithEmployee;

    public function index(): View
    {
        $employee = $this->employee();

        return view('meetings.index', [
            'employee' => $employee,
            'meetings' => TeamMeeting::query()
                ->where('company_id', $employee->company_id)
                ->where(function (Builder $query) use ($employee): void {
                    $query->whereNull('branch_id')
                        ->orWhere('branch_id', $employee->branch_id)
                        ->orWhereIn('id', $employee->teamMeetings()->select('team_meetings.id'));
                })
                ->latest('meeting_date')
                ->paginate(15),
        ]);
    }
}
