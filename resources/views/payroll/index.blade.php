@extends('layouts.app')

@section('title', 'Payroll - Teamiy Connect')
@section('page', 'payroll')
@section('page_title', 'Payroll')

@section('content')
    <div class="wrap">
        <section class="hero"><div class="blob"></div><div class="z"><div class="date">Salary and payroll</div><div class="greet">My Payroll</div><div class="summary">Salary setup, generated payrolls, and payslips.</div></div></section>
        <div class="cards-grid auto-250" style="margin-top:18px">
            <div class="card card-pad stat"><div class="lbl">Salary Cycle</div><div class="val" style="font-size:22px">{{ $employee->employeeAccount->salary_cycle ?? '-' }}</div></div>
            <div class="card card-pad stat"><div class="lbl">Annual Salary</div><div class="val" style="font-size:22px">{{ number_format((float) ($employee->employeeSalary->annual_salary ?? 0), 2) }}</div></div>
            <div class="card card-pad stat"><div class="lbl">Payslips</div><div class="val">{{ $employee->payslips->count() }}</div></div>
        </div>
        <div class="card" style="margin-top:18px">
            <div class="table-wrap">
                <table class="table"><thead><tr><th>Range</th><th>Payroll Type</th><th>Worked</th><th>Base</th><th>Net Salary</th><th>Status</th></tr></thead><tbody>
                    @forelse($payrolls as $payroll)
                        <tr><td>{{ $payroll->range ?: '-' }}</td><td>{{ $payroll->payroll_type ?: '-' }}</td><td>{{ $payroll->worked_hours ?: '-' }}</td><td>{{ number_format((float) $payroll->base_salary, 2) }}</td><td>{{ number_format((float) $payroll->net_salary, 2) }}</td><td>@include('partials.status-badge', ['slot' => $payroll->status])</td></tr>
                    @empty
                        <tr><td colspan="6">No generated payroll found.</td></tr>
                    @endforelse
                </tbody></table>
            </div>
            <div class="card-pad">{{ $payrolls->links() }}</div>
        </div>
    </div>
@endsection
