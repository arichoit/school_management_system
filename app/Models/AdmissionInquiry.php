<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdmissionInquiry extends Model
{
    protected $fillable = [
        'student_name',
        'guardian_name',
        'email',
        'phone',
        'class_applied_for',
        'previous_school',
        'message',
        'status',
    ];
}
