# AI Code Agent Instructions (SMS Project)

## 1. What This Project Is
A bilingual (English primary + Bangla support) School Management System featuring a public website and a multi-role authenticated portal built with Laravel Blade and Tailwind CSS.

## 2. Tech Stack & Versions
* **PHP:** 8.3–8.5
* **Framework:** Laravel 13.x
* **Database:** MySQL 8.0+ (InnoDB engine)
* **Styling:** Tailwind CSS (compiled via Vite)
* **Frontend Interactivity:** Vanilla JS + Alpine.js

## 3. Commands
* **DEV:** `composer run dev` (starts PHP server, queue worker, and Vite asset compiler)
* **BUILD:** `npm run build && php artisan optimize`
* **TEST:** `php artisan test`
* **LINT:** `vendor/bin/pint`

## 4. Architecture & Directory Structure
* `routes/web.php`: Public website & auth routes.
* `routes/admin.php`, `routes/teacher.php`, `routes/student.php`, `routes/parent.php`, `routes/accountant.php`: Role-segregated route files.
* `app/Http/Controllers/`: Single-responsibility controllers.
* `resources/views/`: Blade templates structured by layout (`layouts.app`, `layouts.portal`).

## 5. Code Conventions
* Use strict Eloquent query scoping to prevent IDOR.
* Always use FormRequests for validation.
* Return Blade partials or JSON for AJAX fetch requests.

## 6. Hard Rules (Non-Negotiables)
* NEVER bypass CSRF protection. Always include `<meta name="csrf-token">` in Blade layouts.
* NEVER store passwords in plain text (`Hash::make`).
* NEVER hardcode monetary values; use `DECIMAL(10,2)`.

## 7. Week 1 Gotchas
* Forgetting to scope multi-child parent queries through the `parent_student` pivot table.
* Caching localization strings without clearing view cache during bilingual translation updates.