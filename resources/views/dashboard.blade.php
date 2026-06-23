@extends('layouts.app')

@section('title', 'Dashboard · Teamiy Connect')
@section('page', 'dashboard')
@section('page_title', 'Dashboard')

@section('content')
    @php
        /*
        |--------------------------------------------------------------------------
        | Employee Dashboard Data
        |--------------------------------------------------------------------------
        | This dashboard uses attendance_rules() helper.
        | Make sure helper is autoloaded in composer.json.
        */

        $attendanceRules = function_exists('attendance_rules') ? attendance_rules() : [];

        $fullDetails = data_get($attendanceRules, 'full_details', []);
        $employee = data_get($fullDetails, 'employee') ?? auth()->user();

        $userName = $employee->name ?? 'Employee';
        $firstName = explode(' ', $userName)[0] ?? 'Employee';

        $basic = data_get($fullDetails, 'basic', []);
        $companyDetails = data_get($fullDetails, 'company_details', []);
        $salaryDetails = data_get($fullDetails, 'salary_details', []);
        $leaveDetails = data_get($fullDetails, 'leave_details', []);
        $attendanceDetails = data_get($fullDetails, 'attendance_details', []);
        $assetDetails = data_get($fullDetails, 'asset_details', []);
        $otherDetails = data_get($fullDetails, 'other_details', []);
        $counts = data_get($fullDetails, 'counts', []);

        $company = data_get($companyDetails, 'company');
        $branch = data_get($companyDetails, 'branch');
        $department = data_get($companyDetails, 'department');
        $post = data_get($companyDetails, 'post');
        $officeTime = data_get($companyDetails, 'office_time');
        $supervisor = data_get($companyDetails, 'supervisor');

        $employeeAccount = data_get($salaryDetails, 'account');
        $employeeSalary = data_get($salaryDetails, 'salary');
        $payslips = collect(data_get($salaryDetails, 'payslips', []));

        $leaveTypes = collect(data_get($leaveDetails, 'leave_types', []));
        $leaveRequests = collect(data_get($leaveDetails, 'leave_requests', []));
        $timeLeaves = collect(data_get($leaveDetails, 'time_leaves', []));

        $todayAttendances = collect(data_get($attendanceDetails, 'today', []));
        $recentAttendances = collect(data_get($attendanceDetails, 'recent', []));

        $assetAssignments = collect(data_get($assetDetails, 'assignments', []));
        $assets = collect(data_get($assetDetails, 'assets', []));

        $tadas = collect(data_get($otherDetails, 'tadas', []));
        $advanceSalaries = collect(data_get($otherDetails, 'advance_salaries', []));
        $awards = collect(data_get($otherDetails, 'awards', []));
        $teamMeetings = collect(data_get($otherDetails, 'team_meetings', []));
        $realNotices = collect(data_get($otherDetails, 'notices', []));

        $officeOpening = data_get($attendanceRules, 'office_time.opening_time', $officeTime->opening_time ?? '09:00:00');
        $officeClosing = data_get($attendanceRules, 'office_time.closing_time', $officeTime->closing_time ?? '18:00:00');

        $officeOpeningInput = \Carbon\Carbon::parse($officeOpening)->format('H:i');
        $officeClosingInput = \Carbon\Carbon::parse($officeClosing)->format('H:i');

        $canCheckIn = data_get($attendanceRules, 'permissions.can_check_in', false);
        $canCheckOut = data_get($attendanceRules, 'permissions.can_check_out', false);

        $todayCheckInCount = data_get($attendanceRules, 'today_counts.check_in', 0);
        $todayCheckOutCount = data_get($attendanceRules, 'today_counts.check_out', 0);

        $maxCheckIn = data_get($attendanceRules, 'limits.max_check_in', 3);
        $maxCheckOut = data_get($attendanceRules, 'limits.max_check_out', 3);

        $isWithinOfficeTime = data_get($attendanceRules, 'office_time.is_within_office_time', false);

        $leaveBalance = [
            'total' => $employee->leave_allocated ?? 0,
            'pending' => $leaveRequests->where('status', 'pending')->count(),
            'approved' => $leaveRequests->where('status', 'approved')->count(),
            'rejected' => $leaveRequests->where('status', 'rejected')->count(),
            'types' => $leaveTypes->count(),
        ];

        $assetStats = [
            'total' => $counts['assets'] ?? $assetAssignments->count(),
            'assigned' => $assetAssignments->where('status', 'assigned')->count(),
            'returned' => $assetAssignments->where('status', 'returned')->count(),
            'names' => $assets->pluck('name')->filter()->take(3)->implode(' · ') ?: 'No assets assigned',
        ];

        $nextMeetingModel = $teamMeetings->first();

        $nextMeeting = [
            'title' => $nextMeetingModel->title ?? $nextMeetingModel->meeting_title ?? 'No upcoming meeting',
            'time' => isset($nextMeetingModel)
                ? trim(($nextMeetingModel->meeting_date ?? '') . ' · ' . ($nextMeetingModel->meeting_time ?? ''))
                : 'No meeting found',
            'link' => $nextMeetingModel->meeting_link ?? null,
        ];

        $notices = $realNotices->map(function ($notice) {
            $date = $notice->notice_publish_date ?? $notice->created_at ?? null;

            try {
                $date = $date ? \Carbon\Carbon::parse($date)->format('d M Y') : '';
            } catch (\Throwable $e) {
                $date = '';
            }

            return [
                'title' => $notice->title ?? $notice->notice_title ?? 'Notice',
                'category' => $notice->category ?? $notice->notice_type ?? 'General',
                'date' => $date,
                'priority' => $notice->priority ?? 'Normal',
                'read' => $notice->pivot->is_read ?? false,
            ];
        });

        $upcomingHoliday = $upcomingHoliday ?? [
            'title' => 'No upcoming holiday',
            'date' => 'Holiday data not found',
            'remaining' => 'Please connect holidays relation/helper.',
        ];

        /*
        |--------------------------------------------------------------------------
        | Server attendance sessions for JS
        |--------------------------------------------------------------------------
        */

        $jsTodaySessions = $todayAttendances->map(function ($attendance) {
            $checkIn = $attendance->check_in_at ?? $attendance->check_in ?? $attendance->in_time ?? null;
            $checkOut = $attendance->check_out_at ?? $attendance->check_out ?? $attendance->out_time ?? null;

            $inCarbon = null;
            $outCarbon = null;

            try {
                $inCarbon = $checkIn ? \Carbon\Carbon::parse($checkIn) : null;
            } catch (\Throwable $e) {
                $inCarbon = null;
            }

            try {
                $outCarbon = $checkOut ? \Carbon\Carbon::parse($checkOut) : null;
            } catch (\Throwable $e) {
                $outCarbon = null;
            }

            return [
                'date' => $inCarbon ? $inCarbon->format('Y-m-d') : now()->format('Y-m-d'),
                'inTime' => $inCarbon ? $inCarbon->format('h:i:s A') : '—',
                'outTime' => $outCarbon ? $outCarbon->format('h:i:s A') : '',
                'inEpochMs' => $inCarbon ? $inCarbon->timestamp * 1000 : 0,
                'outEpochMs' => $outCarbon ? $outCarbon->timestamp * 1000 : null,
                'durationMs' => ($inCarbon && $outCarbon) ? $outCarbon->diffInMilliseconds($inCarbon) : 0,
            ];
        })->values();
    @endphp

    <div class="wrap">

        {{-- HERO --}}
        <div class="hero">
            <div class="blob"></div>

            <div class="z">
                <div class="date">{{ now()->format('l, d F Y') }}</div>

                <div class="greet">
                    Good morning, {{ $firstName }} 👋
                </div>

                <div class="summary" id="attSummary">
                    {{ data_get($attendanceRules, 'messages.check_in', "You haven't checked in yet — have a great day!") }}
                </div>

                <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:12px">
                    <span class="badge xs">
                        {{ $employee->employee_code ?? 'No employee code' }}
                    </span>

                    <span class="badge xs">
                        {{ ucfirst($employee->employment_type ?? 'Employee') }}
                    </span>

                    <span class="badge xs">
                        {{ $company->name ?? 'No company' }}
                    </span>
                </div>
            </div>

            <div class="hero-actions">
                <button class="hero-btn" id="heroAttBtn" data-action="att-toggle" type="button">
                    Check In
                </button>

                <a class="hero-btn ghost" href="{{ url('/leave?new=1') }}">
                    Request leave
                </a>
            </div>
        </div>

        {{-- TOP CARDS --}}
        <div class="cards-grid auto-250" style="margin-top:18px">

            {{-- ATTENDANCE CARD --}}
            <div class="card card-pad">
                <div class="spread">
                    <span class="lbl">Today's Attendance</span>

                    <span class="ico tint-blue">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="9"></circle>
                            <path d="M12 7v5l3 2"></path>
                        </svg>
                    </span>
                </div>

                <div style="margin-top:14px;display:flex;align-items:baseline;gap:8px">
                    <span style="font-size:22px;font-weight:800" class="tc-num" id="attCardValue">
                        0h 00m 00s 000ms
                    </span>

                    <span class="badge xs" id="attStatusBadge">
                        Not checked in
                    </span>
                </div>

                <div style="font-size:12.5px;color:#94A3B8;margin-top:8px" id="attCardSub">
                    {{ data_get($attendanceRules, 'messages.check_in', 'Tap check in to start your day') }}
                </div>

                <button class="btn btn-primary btn-block" style="margin-top:14px" id="cardAttBtn" data-action="att-toggle"
                    type="button">
                    Check In
                </button>
            </div>

            {{-- SESSION COUNT CARD --}}
            <div class="card card-pad">
                <div class="spread">
                    <span class="lbl">Today Sessions</span>

                    <span class="ico tint-violet">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4.5" width="18" height="16" rx="2.5"></rect>
                            <path d="M3 9h18M8 2.5v4M16 2.5v4"></path>
                        </svg>
                    </span>
                </div>

                <div style="margin-top:14px;display:flex;align-items:baseline;gap:6px">
                    <span style="font-size:28px;font-weight:800" class="tc-num" id="attSessionCount">
                        {{ $todayAttendances->count() }}
                    </span>

                    <span style="font-size:13px;color:#94A3B8;font-weight:600">
                        sessions
                    </span>
                </div>

                <div style="font-size:12.5px;color:#94A3B8;margin-top:8px" id="attSessionSub">
                    Check in {{ $todayCheckInCount }}/{{ $maxCheckIn }} · Check out {{ $todayCheckOutCount }}/{{ $maxCheckOut }}
                </div>
            </div>

            {{-- ATTENDANCE RULES CARD --}}
            <div class="card card-pad">
                <div class="spread">
                    <span class="lbl">Attendance Rules</span>

                    <span class="ico tint-orange">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="9"></circle>
                            <path d="M12 7v5l3 2"></path>
                        </svg>
                    </span>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:14px">
                    <label style="font-size:12px;color:#64748B;font-weight:700">
                        From
                        <input type="time" id="attStartTime" value="{{ $officeOpeningInput }}"
                            style="width:100%;margin-top:6px;border:1px solid #E2E8F0;border-radius:10px;padding:9px 10px">
                    </label>

                    <label style="font-size:12px;color:#64748B;font-weight:700">
                        To
                        <input type="time" id="attEndTime" value="{{ $officeClosingInput }}"
                            style="width:100%;margin-top:6px;border:1px solid #E2E8F0;border-radius:10px;padding:9px 10px">
                    </label>
                </div>

                <label style="font-size:12px;color:#64748B;font-weight:700;display:block;margin-top:10px">
                    Max check-in / check-out per day
                    <input type="number" id="attMaxSessions" value="{{ $maxCheckIn }}" min="1" max="10"
                        style="width:100%;margin-top:6px;border:1px solid #E2E8F0;border-radius:10px;padding:9px 10px">
                </label>

                <div style="font-size:12.5px;color:#94A3B8;margin-top:10px" id="attRuleStatus">
                    Allowed from {{ \Carbon\Carbon::parse($officeOpening)->format('h:i A') }}
                    to {{ \Carbon\Carbon::parse($officeClosing)->format('h:i A') }}
                    · {{ $maxCheckIn }} check-ins and {{ $maxCheckOut }} check-outs allowed.
                </div>
            </div>

            {{-- LEAVE CARD --}}
            <div class="card card-pad clickable" onclick="window.location.href='{{ url('/leave') }}'">
                <div class="spread">
                    <span class="lbl">Leave Summary</span>

                    <span class="ico tint-violet">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M8 2v4M16 2v4M3 10h18"></path>
                            <path d="M5 6h14a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2z"></path>
                        </svg>
                    </span>
                </div>

                <div style="margin-top:14px;display:flex;align-items:baseline;gap:6px">
                    <span style="font-size:28px;font-weight:800" class="tc-num">
                        {{ $leaveBalance['total'] }}
                    </span>

                    <span style="font-size:13px;color:#94A3B8;font-weight:600">
                        days allocated
                    </span>
                </div>

                <div style="font-size:12.5px;color:#94A3B8;margin-top:8px">
                    <strong style="color:#B26A00">{{ $leaveBalance['pending'] }} pending</strong>
                    · Approved {{ $leaveBalance['approved'] }}
                    · Rejected {{ $leaveBalance['rejected'] }}
                    · Types {{ $leaveBalance['types'] }}
                </div>
            </div>

            {{-- ASSETS CARD --}}
            <div class="card card-pad clickable" onclick="window.location.href='{{ url('/assets') }}'">
                <div class="spread">
                    <span class="lbl">My Assets</span>

                    <span class="ico tint-blue">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 6h16v10H4z"></path>
                            <path d="M8 20h8M12 16v4"></path>
                        </svg>
                    </span>
                </div>

                <div style="margin-top:14px;display:flex;align-items:baseline;gap:6px">
                    <span style="font-size:28px;font-weight:800" class="tc-num">
                        {{ $assetStats['total'] }}
                    </span>

                    <span style="font-size:13px;color:#94A3B8;font-weight:600">
                        assigned
                    </span>
                </div>

                <div
                    style="font-size:12.5px;color:#94A3B8;margin-top:8px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                    {{ $assetStats['names'] }}
                </div>
            </div>
        </div>

        {{-- EMPLOYEE FULL DETAILS --}}
        <div class="cards-grid auto-250" style="margin-top:18px">

            {{-- EMPLOYEE PROFILE --}}
            <div class="card card-pad">
                <div class="spread">
                    <span class="lbl">Employee Profile</span>
                    <span class="badge xs">{{ ucfirst($employee->status ?? 'N/A') }}</span>
                </div>

                <div style="margin-top:14px">
                    <div style="font-size:20px;font-weight:800;color:#1E293B">
                        {{ $employee->name ?? 'N/A' }}
                    </div>

                    <div style="font-size:13px;color:#64748B;margin-top:4px">
                        {{ $employee->employee_code ?? 'No employee code' }}
                    </div>
                </div>

                <div style="font-size:12.5px;color:#94A3B8;margin-top:10px;line-height:1.7">
                    Work Email:
                    <strong style="color:#334155">{{ $employee->work_email ?? 'N/A' }}</strong><br>

                    Phone:
                    <strong style="color:#334155">{{ $employee->phone ?? 'N/A' }}</strong><br>

                    Type:
                    <strong style="color:#334155">{{ ucfirst($employee->employment_type ?? 'N/A') }}</strong>
                </div>
            </div>

            {{-- COMPANY DETAILS --}}
            <div class="card card-pad">
                <div class="spread">
                    <span class="lbl">Company Details</span>
                    <span class="ico tint-blue">🏢</span>
                </div>

                <div style="margin-top:14px;font-size:20px;font-weight:800;color:#1E293B">
                    {{ $company->name ?? 'N/A' }}
                </div>

                <div style="font-size:12.5px;color:#94A3B8;margin-top:10px;line-height:1.7">
                    Branch:
                    <strong style="color:#334155">{{ $branch->name ?? 'N/A' }}</strong><br>

                    Department:
                    <strong style="color:#334155">{{ $department->dept_name ?? 'N/A' }}</strong><br>

                    Post:
                    <strong style="color:#334155">{{ $post->post_name ?? 'N/A' }}</strong>
                </div>
            </div>

            {{-- OFFICE TIME --}}
            <div class="card card-pad">
                <div class="spread">
                    <span class="lbl">Office Time</span>

                    <span class="badge xs">
                        {{ $isWithinOfficeTime ? 'Allowed Now' : 'Closed' }}
                    </span>
                </div>

                <div style="margin-top:14px;display:flex;align-items:baseline;gap:8px;flex-wrap:wrap">
                    <span style="font-size:22px;font-weight:800;color:#1E293B">
                        {{ \Carbon\Carbon::parse($officeOpening)->format('h:i A') }}
                    </span>

                    <span style="font-size:13px;color:#94A3B8">
                        to
                    </span>

                    <span style="font-size:22px;font-weight:800;color:#1E293B">
                        {{ \Carbon\Carbon::parse($officeClosing)->format('h:i A') }}
                    </span>
                </div>

                <div style="font-size:12.5px;color:#94A3B8;margin-top:10px;line-height:1.7">
                    Shift:
                    <strong style="color:#334155">{{ $officeTime->shift ?? 'N/A' }}</strong><br>

                    Type:
                    <strong style="color:#334155">{{ $officeTime->shift_type ?? 'N/A' }}</strong><br>

                    Category:
                    <strong style="color:#334155">{{ $officeTime->category ?? 'N/A' }}</strong>
                </div>
            </div>

            {{-- ATTENDANCE LIMIT --}}
            <div class="card card-pad">
                <div class="spread">
                    <span class="lbl">Attendance Limit</span>
                    <span class="ico tint-orange">⏱</span>
                </div>

                <div style="margin-top:14px;display:grid;grid-template-columns:1fr 1fr;gap:12px">
                    <div>
                        <div style="font-size:12px;color:#94A3B8;font-weight:700">
                            Check In
                        </div>

                        <div style="font-size:24px;font-weight:800;color:#1E293B">
                            {{ $todayCheckInCount }}/{{ $maxCheckIn }}
                        </div>
                    </div>

                    <div>
                        <div style="font-size:12px;color:#94A3B8;font-weight:700">
                            Check Out
                        </div>

                        <div style="font-size:24px;font-weight:800;color:#1E293B">
                            {{ $todayCheckOutCount }}/{{ $maxCheckOut }}
                        </div>
                    </div>
                </div>

                <div style="font-size:12.5px;color:#94A3B8;margin-top:10px">
                    {{ data_get($attendanceRules, 'messages.check_in') }}
                </div>
            </div>
        </div>

        {{-- MORE EMPLOYEE DATA --}}
        <div class="cards-grid auto-250" style="margin-top:18px">

            {{-- SALARY ACCOUNT --}}
            <div class="card card-pad">
                <div class="spread">
                    <span class="lbl">Salary Account</span>
                    <span class="ico tint-green">💳</span>
                </div>

                <div style="margin-top:14px;font-size:18px;font-weight:800;color:#1E293B">
                    {{ $employeeAccount->bank_name ?? 'No bank added' }}
                </div>

                <div style="font-size:12.5px;color:#94A3B8;margin-top:10px;line-height:1.7">
                    Account Holder:
                    <strong style="color:#334155">{{ $employeeAccount->account_holder ?? 'N/A' }}</strong><br>

                    Account No:
                    <strong style="color:#334155">{{ $employeeAccount->bank_account_no ?? 'N/A' }}</strong><br>

                    Cycle:
                    <strong style="color:#334155">{{ ucfirst($employeeAccount->salary_cycle ?? 'N/A') }}</strong>
                </div>
            </div>

            {{-- PAYSLIPS --}}
            <div class="card card-pad">
                <div class="spread">
                    <span class="lbl">Payslips</span>
                    <span class="ico tint-orange">🧾</span>
                </div>

                <div style="margin-top:14px;display:flex;align-items:baseline;gap:6px">
                    <span style="font-size:28px;font-weight:800;color:#1E293B">
                        {{ $counts['payslips'] ?? $payslips->count() }}
                    </span>

                    <span style="font-size:13px;color:#94A3B8;font-weight:600">
                        records
                    </span>
                </div>

                <div style="font-size:12.5px;color:#94A3B8;margin-top:10px">
                    Advance salaries: {{ $counts['advance_salaries'] ?? $advanceSalaries->count() }}
                </div>
            </div>

            {{-- TADA --}}
            <div class="card card-pad clickable" onclick="window.location.href='{{ url('/tada') }}'">
                <div class="spread">
                    <span class="lbl">TADA Requests</span>
                    <span class="ico tint-green">🚕</span>
                </div>

                <div style="margin-top:14px;display:flex;align-items:baseline;gap:6px">
                    <span style="font-size:28px;font-weight:800;color:#1E293B">
                        {{ $counts['tadas'] ?? $tadas->count() }}
                    </span>

                    <span style="font-size:13px;color:#94A3B8;font-weight:600">
                        requests
                    </span>
                </div>

                <div style="font-size:12.5px;color:#94A3B8;margin-top:10px">
                    Latest TADA records from your employee account.
                </div>
            </div>

            {{-- AWARDS --}}
            <div class="card card-pad">
                <div class="spread">
                    <span class="lbl">Awards</span>
                    <span class="ico tint-violet">🏆</span>
                </div>

                <div style="margin-top:14px;display:flex;align-items:baseline;gap:6px">
                    <span style="font-size:28px;font-weight:800;color:#1E293B">
                        {{ $counts['awards'] ?? $awards->count() }}
                    </span>

                    <span style="font-size:13px;color:#94A3B8;font-weight:600">
                        awards
                    </span>
                </div>

                <div style="font-size:12.5px;color:#94A3B8;margin-top:10px">
                    Recognition and achievement records.
                </div>
            </div>

            {{-- MEETINGS --}}
            <div class="card card-pad clickable" onclick="window.location.href='{{ url('/meetings') }}'">
                <div class="spread">
                    <span class="lbl">Team Meetings</span>
                    <span class="ico tint-blue">🎥</span>
                </div>

                <div style="margin-top:14px;display:flex;align-items:baseline;gap:6px">
                    <span style="font-size:28px;font-weight:800;color:#1E293B">
                        {{ $counts['team_meetings'] ?? $teamMeetings->count() }}
                    </span>

                    <span style="font-size:13px;color:#94A3B8;font-weight:600">
                        meetings
                    </span>
                </div>

                <div style="font-size:12.5px;color:#94A3B8;margin-top:10px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                    Next: {{ $nextMeeting['title'] }}
                </div>
            </div>

            {{-- NOTICES --}}
            <div class="card card-pad clickable" onclick="window.location.href='{{ url('/notices') }}'">
                <div class="spread">
                    <span class="lbl">Notices</span>
                    <span class="ico tint-orange">📢</span>
                </div>

                <div style="margin-top:14px;display:flex;align-items:baseline;gap:6px">
                    <span style="font-size:28px;font-weight:800;color:#1E293B">
                        {{ $counts['notices'] ?? $notices->count() }}
                    </span>

                    <span style="font-size:13px;color:#94A3B8;font-weight:600">
                        notices
                    </span>
                </div>

                <div style="font-size:12.5px;color:#94A3B8;margin-top:10px">
                    Latest company notices assigned to you.
                </div>
            </div>
        </div>

        {{-- ATTENDANCE LOGS --}}
        <div class="card" style="margin-top:16px">
            <div class="spread" style="padding:16px 18px 12px">
                <div>
                    <span class="section-title">Today Check In / Check Out Logs</span>

                    <div style="font-size:12.5px;color:#94A3B8;margin-top:3px">
                        Session logs show check-in time, check-out time and exact duration with milliseconds.
                    </div>
                </div>

                <span class="badge xs">
                    {{ $todayAttendances->count() }} logs
                </span>
            </div>

            <div id="attLogsWrap" style="padding:0 18px 18px">
                @forelse ($todayAttendances as $index => $attendance)
                    @php
                        $checkIn = $attendance->check_in_at ?? $attendance->check_in ?? $attendance->in_time ?? null;
                        $checkOut = $attendance->check_out_at ?? $attendance->check_out ?? $attendance->out_time ?? null;

                        try {
                            $checkInTime = $checkIn ? \Carbon\Carbon::parse($checkIn) : null;
                        } catch (\Throwable $e) {
                            $checkInTime = null;
                        }

                        try {
                            $checkOutTime = $checkOut ? \Carbon\Carbon::parse($checkOut) : null;
                        } catch (\Throwable $e) {
                            $checkOutTime = null;
                        }

                        $durationText = 'Running';

                        if ($checkInTime && $checkOutTime) {
                            $minutes = $checkOutTime->diffInMinutes($checkInTime);
                            $hours = floor($minutes / 60);
                            $mins = $minutes % 60;
                            $durationText = $hours . 'h ' . str_pad($mins, 2, '0', STR_PAD_LEFT) . 'm';
                        }
                    @endphp

                    <div style="display:grid;grid-template-columns:72px 1fr 1fr 1fr auto;gap:12px;align-items:center;padding:14px 0;border-top:1px solid #E2E8F0">
                        <div>
                            <div style="font-size:12px;color:#94A3B8;font-weight:700">Session</div>
                            <div style="font-size:16px;font-weight:800;color:#1E293B">#{{ $index + 1 }}</div>
                        </div>

                        <div>
                            <div style="font-size:12px;color:#94A3B8;font-weight:700">Check In</div>
                            <div style="font-size:13.5px;font-weight:700;color:#1E293B">
                                {{ $checkInTime ? $checkInTime->format('h:i:s A') : '—' }}
                            </div>
                        </div>

                        <div>
                            <div style="font-size:12px;color:#94A3B8;font-weight:700">Check Out</div>
                            <div style="font-size:13.5px;font-weight:700;color:#1E293B">
                                {{ $checkOutTime ? $checkOutTime->format('h:i:s A') : '—' }}
                            </div>
                        </div>

                        <div>
                            <div style="font-size:12px;color:#94A3B8;font-weight:700">Duration</div>
                            <div style="font-size:13.5px;font-weight:800;color:#1E293B">
                                {{ $durationText }}
                            </div>
                        </div>

                        <span class="badge {{ $checkOutTime ? 'badge-green' : 'badge-blue' }}">
                            {{ $checkOutTime ? 'Completed' : 'Running' }}
                        </span>
                    </div>
                @empty
                    <div style="padding:18px;color:#94A3B8;font-size:13px">
                        No attendance log found today.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- RECENT ACTIVITY / BOTTOM GRID --}}
        <div class="dashboard-bottom-grid" style="display:grid;grid-template-columns:1.4fr 1fr;gap:16px;margin-top:16px">

            {{-- LATEST NOTICES --}}
            <div class="card">
                <div class="spread" style="padding:16px 18px 12px">
                    <span class="section-title">Latest Notices</span>
                    <a class="link" style="font-size:12.5px" href="{{ url('/notices') }}">View all</a>
                </div>

                @forelse ($notices->take(4) as $notice)
                    <div class="list-row" onclick="window.location.href='{{ url('/notices') }}'">
                        <span class="dot {{ !empty($notice['read']) ? 'off' : 'on' }}"></span>

                        <div style="flex:1;min-width:0">
                            <div
                                style="font-size:13.5px;font-weight:{{ !empty($notice['read']) ? 600 : 800 }};color:#1E293B;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                                {{ $notice['title'] }}
                            </div>

                            <div style="font-size:12px;color:#94A3B8;margin-top:2px">
                                {{ $notice['category'] }} · {{ $notice['date'] }}
                            </div>
                        </div>

                        <span class="badge">
                            {{ $notice['priority'] }}
                        </span>
                    </div>
                @empty
                    <div style="padding:18px;color:#94A3B8;font-size:13px">
                        No notices found.
                    </div>
                @endforelse
            </div>

            <div style="display:flex;flex-direction:column;gap:16px">

                {{-- NEXT MEETING --}}
                <div class="card card-pad-lg">
                    <div class="spread" style="margin-bottom:10px">
                        <span class="section-title">Next Meeting</span>
                        <a class="link" style="font-size:12.5px" href="{{ url('/meetings') }}">All</a>
                    </div>

                    <div style="font-size:14px;font-weight:700;color:#1E293B">
                        {{ $nextMeeting['title'] }}
                    </div>

                    <div style="font-size:12.5px;color:#94A3B8;margin-top:4px">
                        {{ $nextMeeting['time'] ?: 'No meeting time found' }}
                    </div>

                    @if (!empty($nextMeeting['link']))
                        <a class="btn btn-block" style="margin-top:12px;background:#16A34A;color:#fff"
                            href="{{ $nextMeeting['link'] }}" target="_blank">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M23 7l-7 5 7 5V7z"></path>
                                <rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect>
                            </svg>
                            Join meeting
                        </a>
                    @else
                        <a class="btn btn-block" style="margin-top:12px;background:#16A34A;color:#fff"
                            href="{{ url('/meetings') }}">
                            View meetings
                        </a>
                    @endif
                </div>

                {{-- UPCOMING HOLIDAY --}}
                <div class="holiday-card">
                    <div class="holiday-kicker">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 21h18"></path>
                            <path d="M5 21V8l7-5 7 5v13"></path>
                            <path d="M9 21v-6h6v6"></path>
                        </svg>
                        Upcoming Holiday
                    </div>

                    <div style="font-size:17px;font-weight:800;color:#7A3D12;margin-top:8px">
                        {{ $upcomingHoliday['title'] }}
                    </div>

                    <div style="font-size:13px;color:#A85C2A;margin-top:2px">
                        {{ $upcomingHoliday['date'] }}
                    </div>

                    <div style="font-size:12.5px;color:#C07B45;margin-top:8px;font-weight:600">
                        {{ $upcomingHoliday['remaining'] }}
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function() {
            "use strict";

            let started = false;

            const SERVER_SESSIONS = @json($jsTodaySessions);

            const SETTINGS_KEY = "tc_attendance_rules";

            const DEFAULT_SETTINGS = {
                start: @json($officeOpeningInput),
                end: @json($officeClosingInput),
                max: Number(@json($maxCheckIn))
            };

            function bootTC() {
                if (!window.TC || typeof TC.boot !== "function") return false;

                if (!TC.state) {
                    TC.boot(null);
                }

                if (!TC.state.att) {
                    TC.state.att = {};
                }

                if (!Array.isArray(TC.state.att.sessions)) {
                    TC.state.att.sessions = [];
                }

                if (Array.isArray(SERVER_SESSIONS) && SERVER_SESSIONS.length && !TC.state.att.__serverSeeded) {
                    const hasTodayLocal = TC.state.att.sessions.some(function(session) {
                        return session.date === todayKey();
                    });

                    if (!hasTodayLocal) {
                        TC.state.att.sessions = SERVER_SESSIONS;
                    }

                    TC.state.att.__serverSeeded = true;

                    if (typeof TC.save === "function") {
                        TC.save();
                    }
                }

                return !!TC.state;
            }

            function loadSettings() {
                try {
                    const saved = JSON.parse(localStorage.getItem(SETTINGS_KEY) || "null");

                    return {
                        start: saved && saved.start ? saved.start : DEFAULT_SETTINGS.start,
                        end: saved && saved.end ? saved.end : DEFAULT_SETTINGS.end,
                        max: saved && saved.max ? Number(saved.max) : DEFAULT_SETTINGS.max
                    };
                } catch (e) {
                    return DEFAULT_SETTINGS;
                }
            }

            function saveSettings(settings) {
                localStorage.setItem(SETTINGS_KEY, JSON.stringify(settings));
            }

            function applySettingsToInputs() {
                const settings = loadSettings();

                const startInput = document.getElementById("attStartTime");
                const endInput = document.getElementById("attEndTime");
                const maxInput = document.getElementById("attMaxSessions");

                if (startInput) startInput.value = settings.start;
                if (endInput) endInput.value = settings.end;
                if (maxInput) maxInput.value = settings.max;
            }

            function readSettingsFromInputs() {
                const startInput = document.getElementById("attStartTime");
                const endInput = document.getElementById("attEndTime");
                const maxInput = document.getElementById("attMaxSessions");

                const settings = {
                    start: startInput && startInput.value ? startInput.value : DEFAULT_SETTINGS.start,
                    end: endInput && endInput.value ? endInput.value : DEFAULT_SETTINGS.end,
                    max: maxInput && maxInput.value ? Math.max(1, Number(maxInput.value)) : DEFAULT_SETTINGS.max
                };

                saveSettings(settings);

                return settings;
            }

            function todayKey() {
                const d = new Date();

                return (
                    d.getFullYear() +
                    "-" +
                    String(d.getMonth() + 1).padStart(2, "0") +
                    "-" +
                    String(d.getDate()).padStart(2, "0")
                );
            }

            function timeToMinutes(time) {
                const parts = String(time || "00:00").split(":");

                return (Number(parts[0] || 0) * 60) + Number(parts[1] || 0);
            }

            function nowMinutes() {
                const d = new Date();

                return d.getHours() * 60 + d.getMinutes();
            }

            function isWithinAllowedTime(settings) {
                const start = timeToMinutes(settings.start);
                const end = timeToMinutes(settings.end);
                const now = nowMinutes();

                if (start <= end) {
                    return now >= start && now <= end;
                }

                return now >= start || now <= end;
            }

            function msToText(totalMs) {
                totalMs = Math.max(0, Math.floor(Number(totalMs || 0)));

                const hours = Math.floor(totalMs / 3600000);
                totalMs = totalMs % 3600000;

                const minutes = Math.floor(totalMs / 60000);
                totalMs = totalMs % 60000;

                const seconds = Math.floor(totalMs / 1000);
                const milliseconds = totalMs % 1000;

                return (
                    hours +
                    "h " +
                    String(minutes).padStart(2, "0") +
                    "m " +
                    String(seconds).padStart(2, "0") +
                    "s " +
                    String(milliseconds).padStart(3, "0") +
                    "ms"
                );
            }

            function getTodaySessions() {
                if (!window.TC || !TC.state || !TC.state.att) return [];

                if (!Array.isArray(TC.state.att.sessions)) {
                    TC.state.att.sessions = [];
                }

                const today = todayKey();

                return TC.state.att.sessions.filter(function(session) {
                    return session.date === today;
                });
            }

            function getActiveSession() {
                const sessions = getTodaySessions();

                for (let i = sessions.length - 1; i >= 0; i--) {
                    if (!sessions[i].outEpochMs) {
                        return sessions[i];
                    }
                }

                return null;
            }

            function getTotalTodayMs() {
                const sessions = getTodaySessions();
                const now = Date.now();

                return sessions.reduce(function(total, session) {
                    if (session.outEpochMs) {
                        return total + Number(session.durationMs || 0);
                    }

                    if (session.inEpochMs) {
                        return total + Math.max(0, now - Number(session.inEpochMs));
                    }

                    return total;
                }, 0);
            }

            function countCompletedCheckouts() {
                return getTodaySessions().filter(function(session) {
                    return !!session.outEpochMs;
                }).length;
            }

            function canPerformAttendanceAction() {
                bootTC();

                const settings = readSettingsFromInputs();
                const sessions = getTodaySessions();
                const active = getActiveSession();
                const completedCheckouts = countCompletedCheckouts();

                if (!isWithinAllowedTime(settings)) {
                    return {
                        ok: false,
                        message: "Attendance allowed only from " + settings.start + " to " + settings.end + "."
                    };
                }

                if (active) {
                    if (completedCheckouts >= settings.max) {
                        return {
                            ok: false,
                            message: "Maximum " + settings.max + " check-outs allowed for today."
                        };
                    }

                    return {
                        ok: true,
                        message: ""
                    };
                }

                if (sessions.length >= settings.max) {
                    return {
                        ok: false,
                        message: "Maximum " + settings.max + " check-ins allowed for today."
                    };
                }

                return {
                    ok: true,
                    message: ""
                };
            }

            function notify(message) {
                if (window.TC && typeof TC.toast === "function") {
                    TC.toast(message);
                    return;
                }

                const root = document.getElementById("toastRoot");

                if (!root) {
                    alert(message);
                    return;
                }

                root.innerHTML = `
                    <div class="toast">
                        <span class="ok">✓</span>
                        ${message}
                    </div>
                `;

                clearTimeout(window.__attRuleToast);

                window.__attRuleToast = setTimeout(function() {
                    root.innerHTML = "";
                }, 2600);
            }

            function renderLiveAttendance() {
                if (!bootTC()) return;

                const settings = loadSettings();
                const sessions = getTodaySessions();
                const active = getActiveSession();
                const totalText = msToText(getTotalTodayMs());
                const completedCheckouts = countCompletedCheckouts();
                const allowedNow = isWithinAllowedTime(settings);

                const label = active ? "Check Out" : "Check In";

                const status = active
                    ? "Checked in"
                    : sessions.length
                        ? "Checked out"
                        : "Not checked in";

                const summary = active
                    ? "You checked in at " + active.inTime + ". Total today: " + totalText + "."
                    : sessions.length
                        ? "You are currently checked out. Total today: " + totalText + "."
                        : "You haven't checked in yet — have a great day!";

                const sub = active
                    ? "Current session started at " + active.inTime + " · Sessions today: " + sessions.length
                    : sessions.length
                        ? sessions.length + " sessions completed today"
                        : "Tap check in to start your day";

                const sessionSub = active
                    ? "One session is currently running"
                    : sessions.length
                        ? "Last session completed"
                        : "No attendance log found today";

                const ruleStatus = allowedNow
                    ? "Allowed now · " + Math.max(0, settings.max - sessions.length) + " check-ins left · " + Math.max(0, settings.max - completedCheckouts) + " check-outs left"
                    : "Not allowed now · Allowed from " + settings.start + " to " + settings.end;

                const attSummary = document.getElementById("attSummary");
                const heroAttBtn = document.getElementById("heroAttBtn");
                const attCardValue = document.getElementById("attCardValue");
                const attStatusBadge = document.getElementById("attStatusBadge");
                const attCardSub = document.getElementById("attCardSub");
                const cardAttBtn = document.getElementById("cardAttBtn");
                const attSessionCount = document.getElementById("attSessionCount");
                const attSessionSub = document.getElementById("attSessionSub");
                const topAtt = document.getElementById("topAtt");
                const topAttLabel = document.getElementById("topAttLabel");
                const attRuleStatus = document.getElementById("attRuleStatus");

                if (attSummary) attSummary.textContent = summary;
                if (heroAttBtn) heroAttBtn.textContent = label;
                if (attCardValue) attCardValue.textContent = totalText;
                if (attStatusBadge) attStatusBadge.textContent = status;
                if (attCardSub) attCardSub.textContent = sub;
                if (cardAttBtn) cardAttBtn.textContent = label;
                if (attSessionCount) attSessionCount.textContent = sessions.length;
                if (attSessionSub) attSessionSub.textContent = sessionSub;
                if (attRuleStatus) attRuleStatus.textContent = ruleStatus;

                if (cardAttBtn) {
                    cardAttBtn.className = "btn " + (active ? "btn-ghost" : "btn-primary") + " btn-block";
                }

                if (topAtt) {
                    topAtt.className = "att-btn " + (active ? "in" : "out");
                }

                if (topAttLabel) {
                    topAttLabel.textContent = label;
                }

                renderAttendanceLogs(sessions);
            }

            function renderAttendanceLogs(sessions) {
                const wrap = document.getElementById("attLogsWrap");

                if (!wrap) return;

                if (!sessions.length) {
                    wrap.innerHTML = `
                        <div style="padding:18px;color:#94A3B8;font-size:13px">
                            No attendance log found today.
                        </div>
                    `;
                    return;
                }

                wrap.innerHTML = sessions.slice().reverse().map(function(session, index) {
                    const sessionNo = sessions.length - index;
                    const isRunning = !session.outEpochMs;

                    const duration = isRunning
                        ? msToText(Date.now() - Number(session.inEpochMs || Date.now()))
                        : msToText(Number(session.durationMs || 0));

                    return `
                        <div style="display:grid;grid-template-columns:72px 1fr 1fr 1fr auto;gap:12px;align-items:center;padding:14px 0;border-top:1px solid #E2E8F0">
                            <div>
                                <div style="font-size:12px;color:#94A3B8;font-weight:700">Session</div>
                                <div style="font-size:16px;font-weight:800;color:#1E293B">#${sessionNo}</div>
                            </div>

                            <div>
                                <div style="font-size:12px;color:#94A3B8;font-weight:700">Check In</div>
                                <div style="font-size:13.5px;font-weight:700;color:#1E293B">${session.inTime || "—"}</div>
                            </div>

                            <div>
                                <div style="font-size:12px;color:#94A3B8;font-weight:700">Check Out</div>
                                <div style="font-size:13.5px;font-weight:700;color:#1E293B">${session.outTime || "—"}</div>
                            </div>

                            <div>
                                <div style="font-size:12px;color:#94A3B8;font-weight:700">Duration</div>
                                <div style="font-size:13.5px;font-weight:800;color:#1E293B">${duration}</div>
                            </div>

                            <span class="badge ${isRunning ? "badge-blue" : "badge-green"}">
                                ${isRunning ? "Running" : "Completed"}
                            </span>
                        </div>
                    `;
                }).join("");
            }

            function startLiveTimer() {
                if (started) return;
                started = true;

                renderLiveAttendance();

                setInterval(function() {
                    renderLiveAttendance();
                }, 1000);
            }

            document.addEventListener("click", function(e) {
                const attBtn = e.target.closest("[data-action='att-toggle']");

                if (!attBtn) return;

                const result = canPerformAttendanceAction();

                if (!result.ok) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();

                    notify(result.message);
                    renderLiveAttendance();
                    return false;
                }

                setTimeout(function() {
                    renderLiveAttendance();
                }, 30);
            }, true);

            document.addEventListener("DOMContentLoaded", function() {
                bootTC();
                applySettingsToInputs();

                const startInput = document.getElementById("attStartTime");
                const endInput = document.getElementById("attEndTime");
                const maxInput = document.getElementById("attMaxSessions");

                [startInput, endInput, maxInput].forEach(function(input) {
                    if (!input) return;

                    input.addEventListener("change", function() {
                        readSettingsFromInputs();
                        renderLiveAttendance();
                        notify("Attendance rules updated.");
                    });
                });

                startLiveTimer();
            });

            window.addEventListener("load", function() {
                bootTC();
                applySettingsToInputs();
                startLiveTimer();
            });
        })();
    </script>
@endpush