<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Employee\Concerns\WorksWithEmployee;
use App\Models\GeneratedPayroll;
use Illuminate\Contracts\View\View;

class PayrollController extends Controller
{
    use WorksWithEmployee;

    public function index(): View
    {
        $employee = $this->employee();

        return view('payroll.index', [
            'employee' => $employee->load(['employeeAccount', 'employeeSalary', 'payslips']),
            'payrolls' => GeneratedPayroll::query()
                ->where('employee_id', $employee->id)
                ->latest()
                ->paginate(15),
        ]);
    }
}
