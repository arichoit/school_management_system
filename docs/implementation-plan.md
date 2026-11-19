# Implementation Plan: Laravel SMS

**Sequence Strategy:** Build core database and models first, followed by public website, authentication, role portals, and payment integrations.

1. **Step 1: Database Migrations & Seeders [Size: M]**
   * *Files Touched:* `database/migrations/*`, `database/seeders/*`
   * *Changes:* Create tables for users, academic years, classes, students, attendance, fees. Seed default Super Admin.
   * *Verification:* `php artisan migrate:fresh --seed` runs without errors.
2. **Step 2: Public Website & Admission Inquiry [Size: S]**
   * *Files Touched:* `routes/web.php`, `app/Http/Controllers/PublicController.php`, `resources/views/welcome.blade.php`
   * *Changes:* Build public landing page, notice board, and admission form.
   * *Verification:* Visit `/` and submit test admission inquiry.
3. **Step 3: Role-Based Authentication & Middleware [Size: M]**
   * *Files Touched:* `app/Http/Middleware/RoleMiddleware.php`, `routes/admin.php`, `routes/teacher.php`, etc.
   * *Changes:* Restrict portal access by user role.
   * *Verification:* Test login redirects for each role.
4. **Step 4: Academic SIS Modules (Students, Attendance, Grades) [Size: L]**
   * *Files Touched:* `app/Http/Controllers/Admin/`, `resources/views/admin/`
   * *Changes:* CRUD for students, class attendance marking, exam grading.
   * *Verification:* Complete end-to-end grading flow for a test student.
5. **Step 5: Fee Management & Payment Gateways [Size: L]**
   * *Files Touched:* `app/Http/Controllers/Accountant/`, `app/Services/PaymentService.php`
   * *Changes:* Invoice generation, manual receipt logging, Stripe/bKash webhook handlers.
   * *Verification:* Simulate online fee payment and receipt generation.