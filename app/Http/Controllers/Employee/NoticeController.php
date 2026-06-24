<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Employee\Concerns\WorksWithEmployee;
use App\Models\Notice;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class NoticeController extends Controller
{
    use WorksWithEmployee;

    public function index(): View
    {
        $employee = $this->employee();

        return view('notices.index', [
            'employee' => $employee,
            'notices' => Notice::query()
                ->where('company_id', $employee->company_id)
                ->where('is_active', true)
                ->where(function (Builder $query) use ($employee): void {
                    $query->whereNull('branch_id')
                        ->orWhere('branch_id', $employee->branch_id)
                        ->orWhereIn('id', $employee->notices()->select('notices.id'));
                })
                ->latest('notice_publish_date')
                ->paginate(15),
        ]);
    }
}
