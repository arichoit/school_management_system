<?php

namespace App\Events;

use App\Models\Attendance;

class AttendanceSubmitted
{
    public function __construct(
        public readonly Attendance $attendance,
        public readonly int $markedBy,
    ) {}
}