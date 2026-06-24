<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class StoreResignationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'resignation_date' => ['required', 'date'],
            'last_working_day' => ['required', 'date', 'after_or_equal:resignation_date'],
            'reason' => ['required', 'string', 'max:5000'],
        ];
    }
}
