<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Employee\Concerns\WorksWithEmployee;
use App\Models\Holiday;
use Illuminate\Contracts\View\View;

class HolidayController extends Controller
{
    use WorksWithEmployee;

    public function index(): View
    {
        $employee = $this->employee();

        return view('holidays.index', [
            'employee' => $employee,
            'holidays' => Holiday::query()
                ->where('company_id', $employee->company_id)
                ->where('is_active', true)
                ->orderBy('event_date')
                ->paginate(20),
            'nextHoliday' => Holiday::query()
                ->where('company_id', $employee->company_id)
                ->where('is_active', true)
                ->whereDate('event_date', '>=', today())
                ->orderBy('event_date')
                ->first(),
        ]);
    }
}
