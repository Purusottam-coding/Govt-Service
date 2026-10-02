<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Department;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BranchAndUniqueIdTest extends TestCase
{
    use RefreshDatabase;

    public function test_department_code_can_be_set_and_auto_generated(): void
    {
        $dept1 = Department::create([
            'name' => 'यातायात व्यवस्था विभाग',
            'code' => 'YAT',
            'status' => true,
        ]);

        $this->assertEquals('YAT', $dept1->code);

        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.departments.store'), [
            'name' => 'योजना तथा बजेट',
            'code' => 'PLAN',
            'status' => '1',
        ]);

        $response->assertRedirect(route('admin.departments.index'));
        $this->assertDatabaseHas('departments', [
            'name' => 'योजना तथा बजेट',
            'code' => 'PLAN',
        ]);
    }

    public function test_unique_certificate_id_includes_branch_code(): void
    {
        $dept = Department::create([
            'name' => 'स्वास्थ्य तथा जनसंख्या',
            'code' => 'HEALTH',
            'status' => true,
        ]);

        $certId = Application::generateUniqueCertificateId($dept);

        $this->assertStringStartsWith('HEALTH', $certId);
        $this->assertMatchesRegularExpression('/^HEALTH[1-9]{3}$/', $certId);
    }

    public function test_public_and_citizen_can_verify_application_by_certificate_id(): void
    {
        $dept = Department::create([
            'name' => 'यातायात व्यवस्था विभाग',
            'code' => 'YAT',
            'status' => true,
        ]);

        $service = Service::create([
            'department_id' => $dept->id,
            'name' => 'सवारी चालक अनुमतिपत्र',
            'fee' => 1000,
            'status' => true,
        ]);

        $user = User::factory()->create(['role' => 'citizen']);
        $otherCitizen = User::factory()->create(['role' => 'citizen']);

        $app = Application::create([
            'user_id' => $user->id,
            'service_id' => $service->id,
            'applicant_name' => 'राम बहादुर',
            'applicant_email' => 'ram@test.np',
            'status' => 'approved',
            'certificate_number' => 'YAT203',
        ]);

        // 1. Unauthenticated guest is redirected to login
        $guestResponse = $this->get(route('public.verify', ['query' => 'YAT203']));
        $guestResponse->assertRedirect(route('login'));

        // 2. Logged-in owner citizen can verify their document
        $ownerResponse = $this->actingAs($user)->get(route('public.verify', ['query' => 'YAT203']));
        $ownerResponse->assertStatus(200);
        $ownerResponse->assertSee('YAT203');
        $ownerResponse->assertSee('राम बहादुर');
        $ownerResponse->assertSee('यातायात व्यवस्था विभाग');

        // 3. Another citizen cannot view someone else's verified document
        $otherResponse = $this->actingAs($otherCitizen)->get(route('public.verify', ['query' => 'YAT203']));
        $otherResponse->assertStatus(200);
        $otherResponse->assertSee('भेटिएन');
        $otherResponse->assertDontSee('राम बहादुर');
    }
}
