<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\ExamGrade;
use App\Models\ExamTerm;
use App\Models\FeeInvoice;
use App\Models\ParentProfile;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Database\Seeders\InitialSetupSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchoolManagementSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(InitialSetupSeeder::class);
    }

    public function test_public_homepage_renders_successfully(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Apex International Academy');
        $response->assertSee('Online Admission Inquiry');
    }

    public function test_public_admission_inquiry_can_be_submitted(): void
    {
        $response = $this->post('/admission-inquiry', [
            'student_name' => 'Tanvir Ahmed',
            'guardian_name' => 'Mohammad Ahmed',
            'email' => 'tanvir.ahmed@example.com',
            'phone' => '+8801700000001',
            'class_applied_for' => 'Class 6',
            'previous_school' => 'Dhaka Residential Model College',
            'message' => 'Please provide information regarding transportation.',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('admission_inquiries', [
            'email' => 'tanvir.ahmed@example.com',
            'student_name' => 'Tanvir Ahmed',
        ]);
    }

    public function test_bilingual_locale_switch_works(): void
    {
        $response = $this->get('/locale/bn');
        $response->assertSessionHas('locale', 'bn');

        // Request with Bengali locale
        $homeResponse = $this->withSession(['locale' => 'bn'])->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('এপেক্স ইন্টারন্যাশনাল একাডেমি');
    }

    public function test_login_redirects_to_correct_role_dashboard(): void
    {
        // 1. Admin login
        $adminResp = $this->post('/login', [
            'email' => 'admin@school.test',
            'password' => 'password',
        ]);
        $adminResp->assertRedirect(route('admin.dashboard'));

        // 2. Teacher login
        $this->post('/logout');
        $teacherResp = $this->post('/login', [
            'email' => 'teacher@school.test',
            'password' => 'password',
        ]);
        $teacherResp->assertRedirect(route('teacher.dashboard'));

        // 3. Parent login
        $this->post('/logout');
        $parentResp = $this->post('/login', [
            'email' => 'parent@school.test',
            'password' => 'password',
        ]);
        $parentResp->assertRedirect(route('parent.dashboard'));

        // 4. Student login
        $this->post('/logout');
        $studentResp = $this->post('/login', [
            'email' => 'student@school.test',
            'password' => 'password',
        ]);
        $studentResp->assertRedirect(route('student.dashboard'));

        // 5. Accountant login
        $this->post('/logout');
        $accResp = $this->post('/login', [
            'email' => 'accountant@school.test',
            'password' => 'password',
        ]);
        $accResp->assertRedirect(route('accountant.dashboard'));
    }

    public function test_role_authorization_prevents_unauthorized_access(): void
    {
        $student = User::where('role', 'student')->first();

        // Student should receive 403 Forbidden on admin dashboard
        $response = $this->actingAs($student)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_teacher_can_batch_submit_attendance(): void
    {
        $teacher = User::where('role', 'teacher')->first();
        $class = SchoolClass::first();
        $section = $class->sections->first();
        $student = Student::first();

        $response = $this->actingAs($teacher)->post('/teacher/attendance', [
            'class_id' => $class->id,
            'section_id' => $section->id,
            'date' => '2026-11-20',
            'attendance' => [
                $student->id => 'present',
            ],
        ]);

        $response->assertSessionHas('success');
        $att = Attendance::where('student_id', $student->id)->whereDate('date', '2026-11-20')->first();
        $this->assertNotNull($att);
        $this->assertEquals('present', $att->status);
    }

    public function test_teacher_can_save_exam_grades(): void
    {
        $teacher = User::where('role', 'teacher')->first();
        $term = ExamTerm::first();
        $subject = Subject::first();
        $student = Student::first();

        $response = $this->actingAs($teacher)->post('/teacher/grades', [
            'exam_term_id' => $term->id,
            'subject_id' => $subject->id,
            'marks' => [
                $student->id => 85.50,
            ],
            'remarks' => [
                $student->id => 'Very good performance.',
            ],
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('exam_grades', [
            'student_id' => $student->id,
            'subject_id' => $subject->id,
            'exam_term_id' => $term->id,
            'marks_obtained' => 85.50,
            'letter_grade' => 'A+',
        ]);
    }

    public function test_parent_child_switcher_and_idor_protection(): void
    {
        $parent = User::where('role', 'parent')->first();
        $parentProfile = $parent->parentProfile;
        $children = $parentProfile->students;

        $this->assertGreaterThanOrEqual(2, $children->count(), 'Parent should have at least 2 children for testing multi-child switcher');

        $child1 = $children[0];
        $child2 = $children[1];

        // 1. Switch to child 2
        $switchResp = $this->actingAs($parent)->post("/parent/switch-child/{$child2->id}");
        $switchResp->assertSessionHas('active_child_id', $child2->id);

        // 2. IDOR Prevention: Attempting to switch to an unrelated student must fail with 404/403
        $unrelatedUser = User::create([
            'name' => 'Stranger Student',
            'email' => 'stranger@school.test',
            'role' => 'student',
            'password' => 'password',
            'is_active' => true,
        ]);
        $unrelatedStudent = Student::create([
            'user_id' => $unrelatedUser->id,
            'admission_number' => 'ADM-STRANGER-999',
            'gender' => 'male',
        ]);

        $idorResp = $this->actingAs($parent)->post("/parent/switch-child/{$unrelatedStudent->id}");
        $idorResp->assertStatus(404);
    }

    public function test_parent_can_pay_fee_invoice(): void
    {
        $parent = User::where('role', 'parent')->first();
        $parentProfile = $parent->parentProfile;
        $child = $parentProfile->students->first();

        $invoice = FeeInvoice::where('student_id', $child->id)->where('status', '!=', 'paid')->first();

        $this->assertNotNull($invoice, 'There should be an unpaid invoice for testing');

        $response = $this->actingAs($parent)->post("/parent/fees/{$invoice->id}/pay", [
            'payment_method' => 'bkash',
            'amount' => $invoice->due_amount,
        ]);

        $response->assertSessionHas('success');
        $invoice->refresh();
        $this->assertEquals('paid', $invoice->status);
        $this->assertEquals(0, $invoice->due_amount);
        $this->assertDatabaseHas('fee_payments', [
            'fee_invoice_id' => $invoice->id,
            'payment_method' => 'bkash',
            'status' => 'success',
        ]);
    }

    public function test_admin_can_admit_student(): void
    {
        $admin = User::where('role', 'admin')->first();
        $class = SchoolClass::first();
        $section = $class->sections->first();
        $academicYear = AcademicYear::first();

        $response = $this->actingAs($admin)->post('/admin/students', [
            'name' => 'Khadija Akhter',
            'email' => 'khadija.akhter@school.test',
            'gender' => 'female',
            'date_of_birth' => '2013-06-18',
            'blood_group' => 'A+',
            'academic_year_id' => $academicYear->id,
            'class_id' => $class->id,
            'section_id' => $section->id,
            'roll_number' => 25,
        ]);

        $response->assertRedirect(route('admin.students.index'));
        $this->assertDatabaseHas('users', ['email' => 'khadija.akhter@school.test']);
        $this->assertDatabaseHas('enrollments', ['roll_number' => 25]);
    }

    public function test_student_report_card_view(): void
    {
        $studentUser = User::where('role', 'student')->first();

        $response = $this->actingAs($studentUser)->get('/student/grades');
        $response->assertStatus(200);
        $response->assertSee('Academic Report Card');
        $response->assertSee('Mathematics');
        $response->assertSee('Term GPA');
    }
}
