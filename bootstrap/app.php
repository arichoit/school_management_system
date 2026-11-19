<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware(['web', 'auth', 'role:admin'])
                ->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));

            Route::middleware(['web', 'auth', 'role:teacher'])
                ->prefix('teacher')
                ->name('teacher.')
                ->group(base_path('routes/teacher.php'));

            Route::middleware(['web', 'auth', 'role:parent'])
                ->prefix('parent')
                ->name('parent.')
                ->group(base_path('routes/parent.php'));

            Route::middleware(['web', 'auth', 'role:student'])
                ->prefix('student')
                ->name('student.')
                ->group(base_path('routes/student.php'));

            Route::middleware(['web', 'auth', 'role:accountant'])
                ->prefix('accountant')
                ->name('accountant.')
                ->group(base_path('routes/accountant.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);

        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
