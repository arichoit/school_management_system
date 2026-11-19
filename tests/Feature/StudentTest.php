<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_student_list(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.students.index'));

        $response->assertStatus(200);
    }

    public function test_guest_cannot_access_student_list(): void
    {
        $response = $this->get(
            route('admin.students.index')
        );

        $response->assertRedirect('/login');
    }
}