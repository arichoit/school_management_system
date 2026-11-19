<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teacher extends Model
{
    protected $fillable = [
        'user_id',
        'employee_id',
        'qualification',
        'specialization',
        'joining_date',
    ];

    protected $casts = [
        'joining_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class, 'class_teacher_id', 'user_id');
    }

    public function timetables(): HasMany
    {
        return $this->hasMany(Timetable::class, 'teacher_id', 'user_id');
    }

    public function homeworks(): HasMany
    {
        return $this->hasMany(Homework::class, 'teacher_id', 'user_id');
    }
}
