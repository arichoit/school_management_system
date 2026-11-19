<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttendanceBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'class_id' => ['required', 'exists:classes,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'date' => ['required', 'date'],
            'attendance' => ['required', 'array', 'min:1'],
            'attendance.*' => ['required', 'in:present,absent,late,excused'],
        ];
    }

    public function messages(): array
    {
        return [
            'attendance.*.in' => 'Attendance status must be one of: present, absent, late, excused.',
        ];
    }
}