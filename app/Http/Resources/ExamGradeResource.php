<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExamGradeResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'subject_id' => $this->subject_id,
            'exam_term_id' => $this->exam_term_id,
            'marks_obtained' => (float) $this->marks_obtained,
            'total_marks' => (float) $this->total_marks,
            'letter_grade' => $this->letter_grade,
            'grade_point' => (float) $this->grade_point,
            'percentage' => $this->total_marks > 0 ? round(($this->marks_obtained / $this->total_marks) * 100, 2) : 0.0,
            'remarks' => $this->remarks,
            'entered_by' => $this->entered_by,
            'student' => $this->whenLoaded('student', function () {
                return [
                    'id' => $this->student->id,
                    'admission_number' => $this->student->admission_number,
                    'name' => $this->student->user->name ?? null,
                ];
            }),
            'subject' => $this->whenLoaded('subject', function () {
                return [
                    'id' => $this->subject->id,
                    'name' => $this->subject->name,
                    'code' => $this->subject->code,
                ];
            }),
            'exam_term' => $this->whenLoaded('examTerm', function () {
                return [
                    'id' => $this->examTerm->id,
                    'name' => $this->examTerm->name,
                ];
            }),
            'entered_by_user' => $this->whenLoaded('enteredBy', function () {
                return [
                    'id' => $this->enteredBy->id,
                    'name' => $this->enteredBy->name,
                ];
            }),
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}