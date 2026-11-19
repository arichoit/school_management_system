<?php

namespace App\Providers;

use App\Events\AttendanceSubmitted;
use App\Events\InvoiceGenerated;
use App\Events\PaymentReceived;
use App\Listeners\LogAttendanceSubmission;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        AttendanceSubmitted::class => [
            LogAttendanceSubmission::class,
        ],
        InvoiceGenerated::class => [],
        PaymentReceived::class => [],
    ];

    public function boot(): void
    {
        //
    }

    public function register(): void
    {
        parent::register();
    }
}