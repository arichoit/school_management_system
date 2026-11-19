<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    public function toArray($request): array
    {
        $enrollment = $this->currentEnrollment;

        return [
            'id' => $this->id,
            'admission_number' => $this->admission_number,
            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth?->toDateString(),
            'blood_group' => $this->blood_group,
            'address' => $this->address,
            'emergency_contact' => $this->emergency_contact,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'phone' => $this->user->phone,
                'is_active' => $this->user->is_active,
            ],
            'class' => $enrollment ? [
                'id' => $enrollment->schoolClass->id,
                'name' => $enrollment->schoolClass->name,
                'code' => $enrollment->schoolClass->code,
            ] : null,
            'section' => $enrollment ? [
                'id' => $enrollment->section->id,
                'name' => $enrollment->section->name,
                'room_number' => $enrollment->section->room_number,
            ] : null,
            'roll_number' => $enrollment?->roll_number,
            'parents' => ParentProfileResource::collection($this->whenLoaded('parents')),
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}