# Product Requirements Document: Responsive School Management System (SMS)

## Feature: End-to-End School Management Portal & Public Website
**Users:** Super Admin / Principal, Teachers, Students, Parents, Accountants, Public Visitors
**Stack:** Laravel (Blade), Tailwind CSS, MySQL, Alpine.js (for reactive UI components)

## 1. Problem Statement & Success Metrics
* **Problem:** Traditional schools suffer from fragmented administrative workflows, delayed fee collections, zero visibility for parents regarding daily attendance/grades, and bloated legacy software that fails on mobile devices.
* **Success Metrics:** 
  * 100% digital fee tracking with online payment reconciliation under 2 seconds.
  * Teacher attendance submission completed in under 60 seconds per class via mobile browser.
  * Zero IDOR security incidents across parent-child data boundaries.
  * Sub-1.5s page load times across desktop and mobile viewports.

## 2. User Stories & Acceptance Criteria
* **As a Super Admin**, I want to configure the academic year, classes, sections, and role permissions so that the institution operates securely.
  * *Acceptance Criteria:* Admin can assign granular roles via Gates/Policies; academic year transitions archive previous records without breaking historical report cards.
* **As a Teacher**, I want to mark student attendance and enter exam grades on my smartphone so that records are updated in real-time.
  * *Acceptance Criteria:* Responsive data tables stack into cards on screens `<768px`; batch submission executes in a single transaction.
* **As a Parent**, I want to view attendance, homework, and fee invoices for all my children from a single login.
  * *Acceptance Criteria:* Parent dashboard features a child switcher dropdown; accessing another child's restricted record triggers an unauthorized 403 response.
* **As an Accountant**, I want to record offline cash/bank payments and verify online gateway transactions (bKash, SSLCommerz, Stripe) so that ledgers balance automatically.
  * *Acceptance Criteria:* Successful gateway webhooks generate downloadable PDF fee receipts instantly.

## 3. Scope: What Ships in v1 vs What Does Not
* **Ships in v1:** Public School Website & Admission Inquiry, Student Admissions & Profile Directory, Classes/Sections/Subjects, Timetable Management, Daily & Period Attendance, Exams & Auto-Generated Report Cards, Fee Structures & Multi-Gateway Invoicing, Homework & Notice Board, Multi-Role Dashboard (Admin, Teacher, Student, Parent, Accountant).
* **Does Not Ship in v1:** Advanced hostel bed allocation, real-time bus GPS tracking, video-conferencing LMS classrooms.

## 4. Data Model Changes (MySQL Schema Highlights)
* `users` (id, name, email, password, role, locale, timestamps)
* `academic_years` (id, name, start_date, end_date, is_active)
* `classes` & `sections` (id, name, class_id, teacher_id)
* `enrollments` (id, student_id, academic_year_id, class_id, section_id, roll_number)
* `attendances` (id, student_id, date, status [present, absent, late, excused], marked_by)
* `exam_grades` (id, student_id, subject_id, exam_term_id, marks_obtained, total_marks)
* `fee_invoices` & `fee_payments` (id, student_id, amount, status, gateway_reference, paid_at)

## 5. Edge Cases & Failure States
* **Network Drop During Attendance:** Local storage caching on mobile browser before `fetch` submission.
* **Concurrent Grade Submission:** Database row-level locks on `exam_grades` composite keys.
* **Partial Fee Payments:** Ledger calculates remaining balance and generates installment logs.

## 6. Open Questions
* *None.* Requirements fully locked during Discovery Mode.