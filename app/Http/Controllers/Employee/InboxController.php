<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Employee\Concerns\WorksWithEmployee;
use App\Models\Notification;
use App\Models\UserNotification;
use Illuminate\Contracts\View\View;

class InboxController extends Controller
{
    use WorksWithEmployee;

    public function index(): View
    {
        $employee = $this->employee();

        return view('inbox.index', [
            'employee' => $employee,
            'userNotifications' => UserNotification::query()
                ->with('notification')
                ->where('user_id', $employee->id)
                ->latest()
                ->paginate(15),
            'generalNotifications' => Notification::query()
                ->where('company_id', $employee->company_id)
                ->where('is_active', true)
                ->latest('notification_publish_date')
                ->limit(8)
                ->get(),
        ]);
    }
}
