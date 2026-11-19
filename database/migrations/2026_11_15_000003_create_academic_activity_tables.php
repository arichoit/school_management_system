<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('section_id')->constrained('sections')->cascadeOnDelete();
            $table->date('date');
            $table->string('status'); // present, absent, late, excused
            $table->string('remarks')->nullable();
            $table->foreignId('marked_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['student_id', 'date'], 'unique_student_daily_attendance');
        });

        Schema::create('exam_terms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->string('name'); // e.g. "Term 1 Examination", "Annual Final Exam"
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('exam_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('exam_term_id')->constrained('exam_terms')->cascadeOnDelete();
            $table->decimal('marks_obtained', 5, 2);
            $table->decimal('total_marks', 5, 2)->default(100.00);
            $table->string('letter_grade', 5)->default('A');
            $table->decimal('grade_point', 3, 2)->default(4.00);
            $table->string('remarks')->nullable();
            $table->foreignId('entered_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['student_id', 'subject_id', 'exam_term_id'], 'unique_student_subject_exam');
        });

        Schema::create('timetables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('section_id')->constrained('sections')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->string('day_of_week'); // Monday, Tuesday, Wednesday, Thursday, Friday
            $table->string('start_time'); // e.g. "09:00"
            $table->string('end_time'); // e.g. "09:45"
            $table->string('room_number')->nullable();
            $table->timestamps();
        });

        Schema::create('homeworks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('section_id')->constrained('sections')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description');
            $table->date('due_date');
            $table->string('attachment_path')->nullable();
            $table->timestamps();
        });

        Schema::create('homework_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('homework_id')->constrained('homeworks')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->text('submission_text')->nullable();
            $table->string('attachment_path')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->string('status')->default('submitted'); // submitted, late, reviewed
            $table->decimal('marks', 5, 2)->nullable();
            $table->text('feedback')->nullable();
            $table->timestamps();
            $table->unique(['homework_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('homework_submissions');
        Schema::dropIfExists('homeworks');
        Schema::dropIfExists('timetables');
        Schema::dropIfExists('exam_grades');
        Schema::dropIfExists('exam_terms');
        Schema::dropIfExists('attendances');
    }
};
