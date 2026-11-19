<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HomeworkSubmission extends Model
{
    protected $table = 'homework_submissions';

    protected $fillable = [
        'homework_id',
        'student_id',
        'submission_text',
        'attachment_path',
        'submitted_at',
        'status',
        'marks',
        'feedback',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'marks' => 'decimal:2',
    ];

    public function homework(): BelongsTo
    {
        return $this->belongsTo(Homework::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
