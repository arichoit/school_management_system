<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'class_id' => $this->class_id,
            'section_id' => $this->section_id,
            'date' => $this->date?->toDateString(),
            'status' => $this->status,
            'remarks' => $this->remarks,
            'marked_by' => $this->marked_by,
            'student' => $this->whenLoaded('student', function () {
                return [
                    'id' => $this->student->id,
                    'admission_number' => $this->student->admission_number,
                    'name' => $this->student->user->name ?? null,
                ];
            }),
            'marked_by_user' => $this->whenLoaded('markedBy', function () {
                return [
                    'id' => $this->markedBy->id,
                    'name' => $this->markedBy->name,
                ];
            }),
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}