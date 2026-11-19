<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\AdmissionInquiry;
use App\Models\Attendance;
use App\Models\ExamGrade;
use App\Models\ExamTerm;
use App\Models\FeeInvoice;
use App\Models\FeePayment;
use App\Models\FeeType;
use App\Models\Homework;
use App\Models\Notice;
use App\Models\ParentProfile;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Timetable;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class InitialSetupSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password');

        // 1. Academic Year
        $academicYear = AcademicYear::firstOrCreate(
            ['name' => '2026-2027'],
            [
                'start_date' => Carbon::create(2026, 1, 1),
                'end_date' => Carbon::create(2026, 12, 31),
                'is_active' => true,
            ]
        );

        // 2. Users (Super Admin, Teacher, Student, Parent, Accountant)
        $admin = User::firstOrCreate(
            ['email' => 'admin@school.test'],
            [
                'name' => 'Dr. Masud Rahman (Principal)',
                'phone' => '+8801711000001',
                'role' => 'admin',
                'locale' => 'en',
                'password' => $password,
                'is_active' => true,
            ]
        );

        $teacherUser = User::firstOrCreate(
            ['email' => 'teacher@school.test'],
            [
                'name' => 'Professor Kamal Hossain',
                'phone' => '+8801711000002',
                'role' => 'teacher',
                'locale' => 'en',
                'password' => $password,
                'is_active' => true,
            ]
        );

        $teacher = Teacher::firstOrCreate(
            ['user_id' => $teacherUser->id],
            [
                'employee_id' => 'EMP-T-1001',
                'qualification' => 'M.Sc. in Mathematics, DU',
                'specialization' => 'Higher Mathematics & Statistics',
                'joining_date' => Carbon::create(2020, 1, 15),
            ]
        );

        $teacherUser2 = User::firstOrCreate(
            ['email' => 'teacher2@school.test'],
            [
                'name' => 'Ms. Nusrat Jahan',
                'phone' => '+8801711000006',
                'role' => 'teacher',
                'locale' => 'en',
                'password' => $password,
                'is_active' => true,
            ]
        );

        $teacher2 = Teacher::firstOrCreate(
            ['user_id' => $teacherUser2->id],
            [
                'employee_id' => 'EMP-T-1002',
                'qualification' => 'M.A. in English Literature',
                'specialization' => 'English Language & Composition',
                'joining_date' => Carbon::create(2021, 3, 1),
            ]
        );

        $accountantUser = User::firstOrCreate(
            ['email' => 'accountant@school.test'],
            [
                'name' => 'Shafiqul Islam (Chief Accountant)',
                'phone' => '+8801711000003',
                'role' => 'accountant',
                'locale' => 'en',
                'password' => $password,
                'is_active' => true,
            ]
        );

        $parentUser = User::firstOrCreate(
            ['email' => 'parent@school.test'],
            [
                'name' => 'Rafiqul Islam Chowdhury',
                'phone' => '+8801711000004',
                'role' => 'parent',
                'locale' => 'en',
                'password' => $password,
                'is_active' => true,
            ]
        );

        $parentProfile = ParentProfile::firstOrCreate(
            ['user_id' => $parentUser->id],
            [
                'occupation' => 'Senior Civil Engineer',
                'secondary_phone' => '+8801811000004',
                'relationship' => 'Father',
            ]
        );

        // 3. Classes and Sections
        $classesData = [
            ['name' => 'Class 6', 'code' => 'C06', 'numeric_level' => 6],
            ['name' => 'Class 7', 'code' => 'C07', 'numeric_level' => 7],
            ['name' => 'Class 8', 'code' => 'C08', 'numeric_level' => 8],
            ['name' => 'Class 9', 'code' => 'C09', 'numeric_level' => 9],
            ['name' => 'Class 10', 'code' => 'C10', 'numeric_level' => 10],
        ];

        $classes = [];
        foreach ($classesData as $c) {
            $classes[$c['code']] = SchoolClass::firstOrCreate(
                ['code' => $c['code']],
                ['name' => $c['name'], 'numeric_level' => $c['numeric_level']]
            );
        }

        $section6A = Section::firstOrCreate(
            ['class_id' => $classes['C06']->id, 'name' => 'Section A'],
            ['room_number' => 'Room 201', 'class_teacher_id' => $teacherUser->id, 'capacity' => 40]
        );

        $section6B = Section::firstOrCreate(
            ['class_id' => $classes['C06']->id, 'name' => 'Section B'],
            ['room_number' => 'Room 202', 'class_teacher_id' => $teacherUser2->id, 'capacity' => 40]
        );

        $section7A = Section::firstOrCreate(
            ['class_id' => $classes['C07']->id, 'name' => 'Section A'],
            ['room_number' => 'Room 203', 'class_teacher_id' => $teacherUser->id, 'capacity' => 40]
        );

        $section8A = Section::firstOrCreate(
            ['class_id' => $classes['C08']->id, 'name' => 'Section A'],
            ['room_number' => 'Room 204', 'class_teacher_id' => $teacherUser2->id, 'capacity' => 40]
        );

        // 4. Subjects
        $subjectsData = [
            ['name' => 'Mathematics', 'code' => 'MATH-101', 'type' => 'theory', 'total_marks' => 100, 'pass_marks' => 33],
            ['name' => 'Bangla', 'code' => 'BAN-101', 'type' => 'theory', 'total_marks' => 100, 'pass_marks' => 33],
            ['name' => 'English', 'code' => 'ENG-101', 'type' => 'theory', 'total_marks' => 100, 'pass_marks' => 33],
            ['name' => 'General Science', 'code' => 'SCI-101', 'type' => 'both', 'total_marks' => 100, 'pass_marks' => 33],
            ['name' => 'Social Science', 'code' => 'SOC-101', 'type' => 'theory', 'total_marks' => 100, 'pass_marks' => 33],
            ['name' => 'Information Technology (ICT)', 'code' => 'ICT-101', 'type' => 'both', 'total_marks' => 50, 'pass_marks' => 20],
        ];

        $subjects = [];
        foreach ($subjectsData as $s) {
            $subjects[$s['code']] = Subject::firstOrCreate(
                ['code' => $s['code']],
                $s
            );
        }

        // Attach subjects to Class 6
        foreach ($subjects as $subj) {
            if (!$classes['C06']->subjects()->where('subjects.id', $subj->id)->exists()) {
                $classes['C06']->subjects()->attach($subj->id, ['teacher_id' => $teacherUser->id]);
            }
            if (!$classes['C07']->subjects()->where('subjects.id', $subj->id)->exists()) {
                $classes['C07']->subjects()->attach($subj->id, ['teacher_id' => $teacherUser2->id]);
            }
        }

        // 5. Students & Parent Linkages
        // Child 1 (Class 6A, Roll 01)
        $studentUser1 = User::firstOrCreate(
            ['email' => 'student@school.test'],
            [
                'name' => 'Rahim Chowdhury',
                'phone' => '+8801711000005',
                'role' => 'student',
                'locale' => 'en',
                'password' => $password,
                'is_active' => true,
            ]
        );

        $student1 = Student::firstOrCreate(
            ['user_id' => $studentUser1->id],
            [
                'admission_number' => 'ADM-2026-001',
                'gender' => 'male',
                'date_of_birth' => Carbon::create(2013, 5, 12),
                'blood_group' => 'B+',
                'address' => 'House 42, Road 11, Dhanmondi, Dhaka',
                'emergency_contact' => '+8801711000004',
            ]
        );

        $student1->enrollments()->firstOrCreate(
            [
                'academic_year_id' => $academicYear->id,
                'class_id' => $classes['C06']->id,
                'section_id' => $section6A->id,
                'roll_number' => 1,
            ],
            ['status' => 'active']
        );

        // Child 2 (Class 8A, Roll 05) - For parent multi-child switcher!
        $studentUser2 = User::firstOrCreate(
            ['email' => 'farhana@school.test'],
            [
                'name' => 'Farhana Chowdhury',
                'phone' => '+8801711000007',
                'role' => 'student',
                'locale' => 'en',
                'password' => $password,
                'is_active' => true,
            ]
        );

        $student2 = Student::firstOrCreate(
            ['user_id' => $studentUser2->id],
            [
                'admission_number' => 'ADM-2026-002',
                'gender' => 'female',
                'date_of_birth' => Carbon::create(2011, 8, 22),
                'blood_group' => 'O+',
                'address' => 'House 42, Road 11, Dhanmondi, Dhaka',
                'emergency_contact' => '+8801711000004',
            ]
        );

        $student2->enrollments()->firstOrCreate(
            [
                'academic_year_id' => $academicYear->id,
                'class_id' => $classes['C08']->id,
                'section_id' => $section8A->id,
                'roll_number' => 5,
            ],
            ['status' => 'active']
        );

        // Attach both children to parent
        $parentProfile->students()->syncWithoutDetaching([$student1->id, $student2->id]);

        // Add 5 more students to Class 6A for realistic attendance & grade roster
        $rosterStudents = [
            ['name' => 'Tanvir Hasan', 'email' => 'tanvir@school.test', 'roll' => 2, 'gender' => 'male'],
            ['name' => 'Ayesha Siddiqua', 'email' => 'ayesha@school.test', 'roll' => 3, 'gender' => 'female'],
            ['name' => 'Sabbir Ahmed', 'email' => 'sabbir@school.test', 'roll' => 4, 'gender' => 'male'],
            ['name' => 'Nabila Karim', 'email' => 'nabila@school.test', 'roll' => 5, 'gender' => 'female'],
            ['name' => 'Mehedi Hasan', 'email' => 'mehedi@school.test', 'roll' => 6, 'gender' => 'male'],
        ];

        $class6Students = [$student1];
        foreach ($rosterStudents as $rs) {
            $u = User::firstOrCreate(
                ['email' => $rs['email']],
                [
                    'name' => $rs['name'],
                    'role' => 'student',
                    'locale' => 'en',
                    'password' => $password,
                    'is_active' => true,
                ]
            );
            $st = Student::firstOrCreate(
                ['user_id' => $u->id],
                [
                    'admission_number' => 'ADM-2026-00' . ($rs['roll'] + 5),
                    'gender' => $rs['gender'],
                    'date_of_birth' => Carbon::create(2013, 1, 10 + $rs['roll']),
                    'blood_group' => 'A+',
                    'address' => 'Mirpur, Dhaka',
                ]
            );
            $st->enrollments()->firstOrCreate(
                [
                    'academic_year_id' => $academicYear->id,
                    'class_id' => $classes['C06']->id,
                    'section_id' => $section6A->id,
                    'roll_number' => $rs['roll'],
                ],
                ['status' => 'active']
            );
            $class6Students[] = $st;
        }

        // 6. Attendance Records
        $today = Carbon::today();
        foreach ($class6Students as $index => $st) {
            // For student1 (Rahim): present
            $status = ($index === 3) ? 'absent' : (($index === 4) ? 'late' : 'present');
            Attendance::updateOrCreate(
                ['student_id' => $st->id, 'date' => $today->toDateString()],
                [
                    'class_id' => $classes['C06']->id,
                    'section_id' => $section6A->id,
                    'status' => $status,
                    'remarks' => $status === 'late' ? '10 mins traffic delay' : null,
                    'marked_by' => $teacherUser->id,
                ]
            );

            // Historical attendance for the past 5 days
            for ($d = 1; $d <= 5; $d++) {
                $pastDate = $today->copy()->subDays($d);
                if (!$pastDate->isWeekend()) {
                    Attendance::updateOrCreate(
                        ['student_id' => $st->id, 'date' => $pastDate->toDateString()],
                        [
                            'class_id' => $classes['C06']->id,
                            'section_id' => $section6A->id,
                            'status' => 'present',
                            'marked_by' => $teacherUser->id,
                        ]
                    );
                }
            }
        }

        // 7. Exam Term & Exam Grades
        $term1 = ExamTerm::firstOrCreate(
            ['academic_year_id' => $academicYear->id, 'name' => 'Term 1 Mid-Term Examination 2026'],
            [
                'start_date' => Carbon::create(2026, 4, 1),
                'end_date' => Carbon::create(2026, 4, 15),
                'is_published' => true,
            ]
        );

        $gradePresets = [
            ['code' => 'MATH-101', 'marks' => 92.00],
            ['code' => 'BAN-101', 'marks' => 84.50],
            ['code' => 'ENG-101', 'marks' => 88.00],
            ['code' => 'SCI-101', 'marks' => 95.00],
            ['code' => 'SOC-101', 'marks' => 78.00],
            ['code' => 'ICT-101', 'marks' => 46.00], // out of 50
        ];

        foreach ($gradePresets as $gp) {
            $sb = $subjects[$gp['code']];
            $pct = ($gp['marks'] / $sb->total_marks) * 100;
            $calc = ExamGrade::calculateGradeAndPoint($pct);

            ExamGrade::updateOrCreate(
                [
                    'student_id' => $student1->id,
                    'subject_id' => $sb->id,
                    'exam_term_id' => $term1->id,
                ],
                [
                    'marks_obtained' => $gp['marks'],
                    'total_marks' => $sb->total_marks,
                    'letter_grade' => $calc['grade'],
                    'grade_point' => $calc['point'],
                    'remarks' => 'Excellent understanding and presentation.',
                    'entered_by' => $teacherUser->id,
                ]
            );
        }

        // Grades for Farhana (Child 2)
        foreach ($gradePresets as $gp) {
            $sb = $subjects[$gp['code']];
            $pct = (($gp['marks'] - 5) / $sb->total_marks) * 100;
            $calc = ExamGrade::calculateGradeAndPoint($pct);

            ExamGrade::updateOrCreate(
                [
                    'student_id' => $student2->id,
                    'subject_id' => $sb->id,
                    'exam_term_id' => $term1->id,
                ],
                [
                    'marks_obtained' => max(0, $gp['marks'] - 5),
                    'total_marks' => $sb->total_marks,
                    'letter_grade' => $calc['grade'],
                    'grade_point' => $calc['point'],
                    'remarks' => 'Very good consistency.',
                    'entered_by' => $teacherUser2->id,
                ]
            );
        }

        // 8. Timetables (Weekly Matrix for Class 6A)
        $schedule = [
            ['day' => 'Monday', 'start' => '09:00', 'end' => '09:45', 'sub' => 'MATH-101', 'room' => '201'],
            ['day' => 'Monday', 'start' => '09:50', 'end' => '10:35', 'sub' => 'ENG-101', 'room' => '201'],
            ['day' => 'Monday', 'start' => '10:40', 'end' => '11:25', 'sub' => 'BAN-101', 'room' => '201'],
            ['day' => 'Monday', 'start' => '11:45', 'end' => '12:30', 'sub' => 'SCI-101', 'room' => 'Lab 1'],
            ['day' => 'Tuesday', 'start' => '09:00', 'end' => '09:45', 'sub' => 'BAN-101', 'room' => '201'],
            ['day' => 'Tuesday', 'start' => '09:50', 'end' => '10:35', 'sub' => 'MATH-101', 'room' => '201'],
            ['day' => 'Tuesday', 'start' => '10:40', 'end' => '11:25', 'sub' => 'SOC-101', 'room' => '201'],
            ['day' => 'Wednesday', 'start' => '09:00', 'end' => '09:45', 'sub' => 'SCI-101', 'room' => 'Lab 1'],
            ['day' => 'Wednesday', 'start' => '09:50', 'end' => '10:35', 'sub' => 'ICT-101', 'room' => 'ICT Lab'],
            ['day' => 'Thursday', 'start' => '09:00', 'end' => '09:45', 'sub' => 'MATH-101', 'room' => '201'],
            ['day' => 'Thursday', 'start' => '09:50', 'end' => '10:35', 'sub' => 'ENG-101', 'room' => '201'],
            ['day' => 'Friday', 'start' => '08:30', 'end' => '09:15', 'sub' => 'SOC-101', 'room' => '201'],
            ['day' => 'Friday', 'start' => '09:20', 'end' => '10:05', 'sub' => 'ICT-101', 'room' => 'ICT Lab'],
        ];

        foreach ($schedule as $slot) {
            Timetable::updateOrCreate(
                [
                    'class_id' => $classes['C06']->id,
                    'section_id' => $section6A->id,
                    'day_of_week' => $slot['day'],
                    'start_time' => $slot['start'],
                ],
                [
                    'end_time' => $slot['end'],
                    'subject_id' => $subjects[$slot['sub']]->id,
                    'teacher_id' => $teacherUser->id,
                    'room_number' => $slot['room'],
                ]
            );
        }

        // 9. Homework & Notice Board
        $hw1 = Homework::firstOrCreate(
            [
                'class_id' => $classes['C06']->id,
                'section_id' => $section6A->id,
                'subject_id' => $subjects['MATH-101']->id,
                'title' => 'Algebra Chapter 4: Linear Equations Practice',
            ],
            [
                'teacher_id' => $teacherUser->id,
                'description' => 'Complete Exercise 4.2 problems 1 through 15 in your homework notebook. Show all derivation steps clearly.',
                'due_date' => Carbon::today()->addDays(3),
            ]
        );

        $hw2 = Homework::firstOrCreate(
            [
                'class_id' => $classes['C06']->id,
                'section_id' => $section6A->id,
                'subject_id' => $subjects['SCI-101']->id,
                'title' => 'Photosynthesis Lab Observation Report',
            ],
            [
                'teacher_id' => $teacherUser->id,
                'description' => 'Write a 2-page summary on chlorophyll activation and leaf starch testing observed during yesterday’s lab session.',
                'due_date' => Carbon::today()->addDays(5),
            ]
        );

        // 10. Fee Types, Invoices and Payments
        $feeType1 = FeeType::firstOrCreate(
            ['academic_year_id' => $academicYear->id, 'title' => 'Tuition Fee - November 2026'],
            [
                'class_id' => $classes['C06']->id,
                'amount' => 4500.00,
                'due_date' => Carbon::create(2026, 11, 20),
            ]
        );

        $feeType2 = FeeType::firstOrCreate(
            ['academic_year_id' => $academicYear->id, 'title' => 'Annual Lab & ICT Facilities Fee'],
            [
                'class_id' => $classes['C06']->id,
                'amount' => 3000.00,
                'due_date' => Carbon::create(2026, 12, 15),
            ]
        );

        // Invoice 1 for Rahim: Paid
        $inv1 = FeeInvoice::firstOrCreate(
            ['invoice_number' => 'INV-2026-00101'],
            [
                'student_id' => $student1->id,
                'fee_type_id' => $feeType1->id,
                'title' => 'Tuition Fee - November 2026',
                'total_amount' => 4500.00,
                'paid_amount' => 4500.00,
                'due_amount' => 0.00,
                'due_date' => Carbon::create(2026, 11, 20),
                'status' => 'paid',
            ]
        );

        FeePayment::firstOrCreate(
            ['receipt_number' => 'REC-2026-9021'],
            [
                'fee_invoice_id' => $inv1->id,
                'amount' => 4500.00,
                'payment_method' => 'bkash',
                'transaction_id' => 'BKS90812X4',
                'status' => 'success',
                'paid_at' => Carbon::create(2026, 11, 10, 14, 25),
                'recorded_by' => $accountantUser->id,
                'notes' => 'Instant bKash payment gateway reconciliation.',
            ]
        );

        // Invoice 2 for Rahim: Unpaid (Available for "Pay Now" trigger)
        $inv2 = FeeInvoice::firstOrCreate(
            ['invoice_number' => 'INV-2026-00102'],
            [
                'student_id' => $student1->id,
                'fee_type_id' => $feeType2->id,
                'title' => 'Annual Lab & ICT Facilities Fee',
                'total_amount' => 3000.00,
                'paid_amount' => 0.00,
                'due_amount' => 3000.00,
                'due_date' => Carbon::create(2026, 12, 15),
                'status' => 'unpaid',
            ]
        );

        // Invoice for Farhana (Child 2): Unpaid
        $inv3 = FeeInvoice::firstOrCreate(
            ['invoice_number' => 'INV-2026-00103'],
            [
                'student_id' => $student2->id,
                'fee_type_id' => $feeType1->id,
                'title' => 'Tuition Fee - November 2026',
                'total_amount' => 4500.00,
                'paid_amount' => 0.00,
                'due_amount' => 4500.00,
                'due_date' => Carbon::create(2026, 11, 25),
                'status' => 'unpaid',
            ]
        );

        // 11. Admission Inquiries
        AdmissionInquiries:
        AdmissionInquiry::firstOrCreate(
            ['email' => 'tariq.hasan@example.com'],
            [
                'student_name' => 'Samir Hasan',
                'guardian_name' => 'Tariq Hasan',
                'phone' => '+8801712345678',
                'class_applied_for' => 'Class 6',
                'previous_school' => 'St. Joseph Higher Secondary School',
                'message' => 'Inquiring regarding curriculum standards and scholarship availability for the 2027 academic session.',
                'status' => 'new',
            ]
        );

        AdmissionInquiry::firstOrCreate(
            ['email' => 'amina.begum@example.com'],
            [
                'student_name' => 'Rifah Nawar',
                'guardian_name' => 'Amina Begum',
                'phone' => '+8801798765432',
                'class_applied_for' => 'Class 9 (Science)',
                'previous_school' => 'Viqarunnisa Noon School & College',
                'message' => 'Looking to transfer due to family relocation to Dhanmondi.',
                'status' => 'contacted',
            ]
        );

        // 12. Notices
        Notice::firstOrCreate(
            ['title' => 'Annual Science Fair 2026 Project Submission Deadline'],
            [
                'content' => 'All students from Classes 6 through 10 are invited to submit their STEM and robotics exhibition proposals to their respective science department leads by the end of next week.',
                'category' => 'academic',
                'is_published_public' => true,
                'target_role' => 'all',
                'published_at' => Carbon::today()->subDays(2),
                'created_by' => $admin->id,
            ]
        );

        Notice::firstOrCreate(
            ['title' => 'Terminal Examination Schedule & Admit Card Distribution'],
            [
                'content' => 'Terminal examination admit cards will be available for download from the student and parent portal starting Monday. Please ensure outstanding tuition dues are cleared.',
                'category' => 'exam',
                'is_published_public' => true,
                'target_role' => 'all',
                'published_at' => Carbon::today()->subDays(4),
                'created_by' => $admin->id,
            ]
        );

        Notice::firstOrCreate(
            ['title' => 'School Closed on Sunday for National Holiday'],
            [
                'content' => 'The school administrative office and classrooms will remain closed on Sunday in observance of the declared national holiday. Normal academic schedules resume Monday.',
                'category' => 'holiday',
                'is_published_public' => true,
                'target_role' => 'all',
                'published_at' => Carbon::today()->subDays(6),
                'created_by' => $admin->id,
            ]
        );
    }
}
