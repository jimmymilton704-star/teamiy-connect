<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLeaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:191'],
            'leave_type_id' => [
                'nullable',
                Rule::exists('leave_types', 'id')->where('company_id', $this->user()?->company_id),
            ],
            'leave_from' => ['required', 'date'],
            'leave_to' => ['required', 'date', 'after_or_equal:leave_from'],
            'reasons' => ['required', 'string', 'max:5000'],
        ];
    }
}
