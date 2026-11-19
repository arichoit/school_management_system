<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignId('class_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->string('title'); // e.g. "Monthly Tuition - November 2026"
            $table->decimal('amount', 10, 2);
            $table->date('due_date')->nullable();
            $table->timestamps();
        });

        Schema::create('fee_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('fee_type_id')->nullable()->constrained('fee_types')->nullOnDelete();
            $table->string('title');
            $table->decimal('total_amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0.00);
            $table->decimal('due_amount', 10, 2);
            $table->date('due_date');
            $table->string('status')->default('unpaid'); // unpaid, partial, paid
            $table->timestamps();
        });

        Schema::create('fee_payments', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_number')->unique();
            $table->foreignId('fee_invoice_id')->constrained('fee_invoices')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('payment_method'); // cash, bank, bkash, sslcommerz, stripe
            $table->string('transaction_id')->nullable();
            $table->string('status')->default('success'); // success, pending, failed
            $table->timestamp('paid_at');
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('admission_inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('student_name');
            $table->string('guardian_name');
            $table->string('email');
            $table->string('phone');
            $table->string('class_applied_for');
            $table->string('previous_school')->nullable();
            $table->text('message')->nullable();
            $table->string('status')->default('new'); // new, contacted, admitted, rejected
            $table->timestamps();
        });

        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->string('category')->default('general'); // general, academic, exam, holiday, admission
            $table->boolean('is_published_public')->default(true);
            $table->string('target_role')->default('all'); // all, admin, teacher, parent, student, accountant
            $table->date('published_at');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notices');
        Schema::dropIfExists('admission_inquiries');
        Schema::dropIfExists('fee_payments');
        Schema::dropIfExists('fee_invoices');
        Schema::dropIfExists('fee_types');
    }
};
