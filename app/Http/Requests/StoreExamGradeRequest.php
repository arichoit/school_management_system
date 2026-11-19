<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExamGradeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'exam_term_id' => ['required', 'exists:exam_terms,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'marks' => ['required', 'array', 'min:1'],
            'marks.*' => ['nullable', 'numeric', 'min:0'],
            'remarks' => ['nullable', 'array'],
            'remarks.*' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $subject = \App\Models\Subject::find($this->input('subject_id'));
            if (!$subject) {
                return;
            }

            foreach ($this->input('marks', []) as $studentId => $marks) {
                if ($marks === null || $marks === '') {
                    continue;
                }
                if ((float) $marks > (float) $subject->total_marks) {
                    $validator->errors()->add("marks.{$studentId}", "Marks cannot exceed total marks ({$subject->total_marks}).");
                }
            }
        });
    }
}