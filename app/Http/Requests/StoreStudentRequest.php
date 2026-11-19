<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'gender' => ['required', 'in:male,female,other'],
            'date_of_birth' => ['required', 'date'],
            'blood_group' => ['nullable', 'string', 'max:5'],
            'address' => ['nullable', 'string', 'max:500'],
            'emergency_contact' => ['nullable', 'string', 'max:20'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'class_id' => ['required', 'exists:classes,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'roll_number' => ['required', 'integer', 'min:1'],
            'parent_id' => ['nullable', 'exists:parents,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'A user with this email address already exists.',
            'gender.in' => 'Gender must be one of: male, female, other.',
            'roll_number.min' => 'Roll number must be at least 1.',
        ];
    }
}