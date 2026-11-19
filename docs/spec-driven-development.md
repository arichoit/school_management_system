# Spec-Driven Development: Student Attendance Module

**Context:** Used by Teachers and Admins to record daily classroom attendance quickly on mobile and desktop.

## 1. Behavior (Given / When / Then)
* **Given** a teacher is logged in and navigates to their assigned class section attendance page,
* **When** they select present/absent for students and click "Save Attendance",
* **Then** attendance records are saved in MySQL in a single transaction, and a success toast notification appears.
* **Edge Case:** If attendance was already submitted for today, the UI displays locked status with an "Edit Request" option.

## 2. API Contract
* `POST /teacher/attendance/store`
  * *Inputs:* `class_id`, `section_id`, `date`, `attendance` (array of `student_id` => `status`).
  * *Outputs:* JSON `{ success: true, message: "Attendance saved successfully." }`
  * *Status Codes:* `200 OK`, `422 Unprocessable Entity`, `403 Forbidden`.

## 3. Data Schema Changes
* Table: `attendances`
  * Columns: `id`, `student_id` (unsignedBigInteger), `class_id`, `section_id`, `date` (date), `status` (enum: present, absent, late, excused), `marked_by` (unsignedBigInteger), timestamps.
  * Index: Unique composite index on `(student_id, date)`.

## 4. UI States
* **Loading:** Skeleton loader on student table rows.
* **Empty:** "No students enrolled in this section."
* **Success:** Green toast notification with undo action.

## 5. Non-Goals
* Biometric fingerprint device integration is excluded from v1.

## 6. Acceptance Checklist
- [ ] Attendance form renders correctly on mobile screens (`<640px`).
- [ ] Duplicate submissions for the same date throw validation errors.
- [ ] Parents receive instantaneous absence notification via database notification queue.