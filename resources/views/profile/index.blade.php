@extends('layouts.app')

@section('title', 'Settings - Teamiy Connect')
@section('page', request()->routeIs('settings.*') ? 'settings' : 'profile')
@section('page_title', 'Settings')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endpush

@section('content')
    @php
        $initials = collect(explode(' ', (string) $employee->name))
            ->filter()
            ->map(fn($part) => strtoupper(substr($part, 0, 1)))
            ->take(2)
            ->implode('');
        $avatar = ltrim((string) ($employee->avatar ?? ''), '/');
        $avatarUrl = null;

        if ($avatar !== '') {
            $avatarUrl =
                str_starts_with($avatar, 'http://') || str_starts_with($avatar, 'https://')
                    ? $avatar
                    : asset(
                        str_starts_with($avatar, 'storage/') || str_starts_with($avatar, 'assets/')
                            ? $avatar
                            : 'storage/' . $avatar,
                    );
        }
    @endphp

    <div class="wrap">
        <section class="settings-hero">
            <div class="settings-identity">
                @if ($avatarUrl)
                    <img class="settings-avatar" src="{{ $avatarUrl }}" alt="{{ $employee->name }}">
                @else
                    <div class="settings-avatar avatar-fallback">{{ $initials ?: 'TC' }}</div>
                @endif

                <div class="settings-heading">
                    <span>{{ $employee->employee_code ?: 'Employee Profile' }}</span>
                    <h1>{{ $employee->name }}</h1>
                    <p>{{ $employee->post?->post_name ?? 'Employee' }} at
                        {{ $employee->company?->name ?? 'Teamiy Connect' }}</p>
                </div>
            </div>

            <div class="settings-hero-meta">
                @include('partials.status-badge', ['slot' => $employee->is_active ? 'active' : 'inactive'])
                <span>{{ $employee->department?->dept_name ?? 'No department' }}</span>
            </div>
        </section>

        @if (session('status'))
            <div class="settings-alert success">{{ session('status') }}</div>
        @endif

        @if (isset($errors) && $errors->any())
            <div class="settings-alert danger">{{ $errors->first() }}</div>
        @endif

        <div class="settings-layout">
            <form class="card settings-form" method="POST" action="{{ route('settings.update') }}"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="settings-card-head">
                    <div>
                        <span>Editable details</span>
                        <h2>Personal Profile</h2>
                    </div>
                    <button class="btn btn-primary" type="submit">Save Changes</button>
                </div>

                <div class="settings-photo-row">
                    @if ($avatarUrl)
                        <img class="settings-photo-preview" src="{{ $avatarUrl }}" alt="{{ $employee->name }}">
                    @else
                        <div class="settings-photo-preview avatar-fallback">{{ $initials ?: 'TC' }}</div>
                    @endif

                    <label class="btn btn-sm" style="background:#EAF5FB;color:var(--primary);display:flex;"><svg width="16"
                            height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5-5 5 5M12 5v12"></path>
                        </svg>Upload new photo<input type="file" accept="image/*" name="avatar" data-action="photo-change"
                            style="display:none"></label>
                </div>

                <div class="settings-form-grid">
                    <label>
                        Full Name
                        <input type="text" name="name" value="{{ old('name', $employee->name) }}" required>
                    </label>

                    <label>
                        Username
                        <input type="text" name="username" value="{{ old('username', $employee->username) }}">
                    </label>

                    <label>
                        Personal Email
                        <input type="email" name="email" value="{{ old('email', $employee->email) }}" required>
                    </label>

                    <label>
                        Work Email
                        <input type="email" name="work_email" value="{{ old('work_email', $employee->work_email) }}">
                    </label>

                    <label>
                        Phone
                        <input type="text" name="phone" value="{{ old('phone', $employee->phone) }}">
                    </label>

                    <label>
                        Date of Birth
                        <input type="date" name="dob" value="{{ old('dob', $employee->dob?->format('Y-m-d')) }}">
                    </label>

                    <label>
                        Gender
                        <select name="gender">
                            <option value="">Select gender</option>
                            @foreach (['male' => 'Male', 'female' => 'Female', 'other' => 'Other'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('gender', $employee->gender) === $value)>{{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </label>

                    <label>
                        Marital Status
                        <select name="marital_status">
                            <option value="">Select status</option>
                            @foreach (['single' => 'Single', 'married' => 'Married', 'divorced' => 'Divorced', 'widowed' => 'Widowed'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('marital_status', $employee->marital_status) === $value)>{{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </label>
                </div>

                <label class="settings-wide-field">
                    Address
                    <textarea name="address" rows="4">{{ old('address', $employee->address) }}</textarea>
                </label>

                <div class="settings-form-actions">
                    <button class="btn btn-primary" type="submit">Save Changes</button>
                </div>
            </form>

            <aside class="settings-side">
                <div class="card settings-summary">
                    <div class="settings-card-head slim">
                        <div>
                            <span>HR information</span>
                            <h2>Employment</h2>
                        </div>
                    </div>

                    <div class="settings-info-list">
                        <div>
                            <span>Company</span>
                            <strong>{{ $employee->company?->name ?? '-' }}</strong>
                        </div>
                        <div>
                            <span>Branch</span>
                            <strong>{{ $employee->branch?->name ?? '-' }}</strong>
                        </div>
                        <div>
                            <span>Department</span>
                            <strong>{{ $employee->department?->dept_name ?? '-' }}</strong>
                        </div>
                        <div>
                            <span>Post</span>
                            <strong>{{ $employee->post?->post_name ?? '-' }}</strong>
                        </div>
                        <div>
                            <span>Supervisor</span>
                            <strong>{{ $employee->supervisor?->name ?? '-' }}</strong>
                        </div>
                        <div>
                            <span>Joining Date</span>
                            <strong>{{ $employee->joining_date?->format('d M Y') ?? '-' }}</strong>
                        </div>
                        <div>
                            <span>Employment Type</span>
                            <strong>{{ $employee->employment_type ?: '-' }}</strong>
                        </div>
                        <div>
                            <span>Contract Type</span>
                            <strong>{{ $employee->contract_type ?: '-' }}</strong>
                        </div>
                        <div>
                            <span>Office Time</span>
                            <strong>{{ $employee->officeTime?->opening_time ?: '-' }} -
                                {{ $employee->officeTime?->closing_time ?: '-' }}</strong>
                        </div>
                    </div>
                </div>

                <div class="card settings-summary">
                    <div class="settings-card-head slim">
                        <div>
                            <span>Payroll reference</span>
                            <h2>Account</h2>
                        </div>
                    </div>

                    <div class="settings-info-list">
                        <div>
                            <span>Bank</span>
                            <strong>{{ $employee->employeeAccount?->bank_name ?? '-' }}</strong>
                        </div>
                        <div>
                            <span>Salary Cycle</span>
                            <strong>{{ $employee->employeeAccount?->salary_cycle ?? '-' }}</strong>
                        </div>
                        <div>
                            <span>Pay Grade</span>
                            <strong>{{ $employee->pay_grade ?: '-' }}</strong>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>
@endsection
