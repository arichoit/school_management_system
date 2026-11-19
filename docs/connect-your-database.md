# Connect Your Database: MySQL + Eloquent ORM

* **Client:** Laravel PDO / Eloquent ORM (Native, optimal for Laravel ecosystem).
* **Environment Variables (`.env` & `.env.example`):**
  ```env
  DB_CONNECTION=mysql
  DB_HOST=127.0.0.1
  DB_PORT=3306
  DB_DATABASE=school_management_db
  DB_USERNAME=root
  DB_PASSWORD=secret
  ```
* **Schema & Relations:**
  * `users` 1:N `students` / `teachers` / `parents`
  * `classes` 1:N `sections`
  * `students` N:N `classes` via `enrollments`
  * `students` 1:N `attendances`, `exam_grades`, `fee_invoices`
* **Migrations & Rollbacks:** Executed via `php artisan migrate`; rollbacks tested via `php artisan migrate:rollback`.
* **Database Seeding Proof:** `php artisan db:seed --class=InitialSetupSeeder` successfully populates test admin and academic year.