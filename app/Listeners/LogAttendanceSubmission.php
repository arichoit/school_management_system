<?php

namespace App\Listeners;

use App\Events\AttendanceSubmitted;
use Illuminate\Support\Facades\Log;

class LogAttendanceSubmission
{
    public function handle(AttendanceSubmitted $event): void
    {
        Log::info('Attendance submitted', [
            'attendance_id' => $event->attendance->id,
            'student_id' => $event->attendance->student_id,
            'date' => $event->attendance->date,
            'status' => $event->attendance->status,
            'marked_by' => $event->markedBy,
        ]);
    }
}