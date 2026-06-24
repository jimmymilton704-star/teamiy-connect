<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Employee\Concerns\WorksWithEmployee;
use Illuminate\Contracts\View\View;

class ProfileController extends Controller
{
    use WorksWithEmployee;

    public function show(): View
    {
        return view('profile.index', [
            'employee' => $this->employee()->load([
                'company',
                'branch',
                'department',
                'post',
                'officeTime',
                'supervisor',
                'employeeAccount',
                'employeeSalary',
            ]),
        ]);
    }
}
