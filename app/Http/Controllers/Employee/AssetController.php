<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Employee\Concerns\WorksWithEmployee;
use App\Models\AssetAssignment;
use App\Models\EmployeeDocument;
use Illuminate\Contracts\View\View;

class AssetController extends Controller
{
    use WorksWithEmployee;

    public function index(): View
    {
        $employee = $this->employee();

        return view('assets.index', [
            'employee' => $employee,
            'assignments' => AssetAssignment::query()
                ->with(['asset.type'])
                ->where('user_id', $employee->id)
                ->latest('assigned_date')
                ->paginate(15),
            'documents' => EmployeeDocument::query()
                ->where('employee_id', $employee->id)
                ->latest()
                ->get(),
        ]);
    }
}
