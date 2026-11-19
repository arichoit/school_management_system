<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamGrade extends Model
{
    protected $fillable = [
        'student_id',
        'subject_id',
        'exam_term_id',
        'marks_obtained',
        'total_marks',
        'letter_grade',
        'grade_point',
        'remarks',
        'entered_by',
    ];

    protected $casts = [
        'marks_obtained' => 'decimal:2',
        'total_marks' => 'decimal:2',
        'grade_point' => 'decimal:2',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function examTerm(): BelongsTo
    {
        return $this->belongsTo(ExamTerm::class);
    }

    public function enteredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entered_by');
    }

    public static function calculateGradeAndPoint(float $percentage): array
    {
        if ($percentage >= 80) {
            return ['grade' => 'A+', 'point' => 5.00];
        } elseif ($percentage >= 70) {
            return ['grade' => 'A', 'point' => 4.00];
        } elseif ($percentage >= 60) {
            return ['grade' => 'A-', 'point' => 3.50];
        } elseif ($percentage >= 50) {
            return ['grade' => 'B', 'point' => 3.00];
        } elseif ($percentage >= 40) {
            return ['grade' => 'C', 'point' => 2.00];
        } elseif ($percentage >= 33) {
            return ['grade' => 'D', 'point' => 1.00];
        } else {
            return ['grade' => 'F', 'point' => 0.00];
        }
    }
}
