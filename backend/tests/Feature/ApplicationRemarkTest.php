<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Department;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationRemarkTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $citizen;
    protected User $otherCitizen;
    protected Application $application;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->citizen = User::factory()->create(['role' => 'citizen']);
        $this->otherCitizen = User::factory()->create(['role' => 'citizen']);

        $dept = Department::create([
            'name' => 'प्रशासन शाखा',
            'code' => 'ADM',
            'status' => true,
        ]);

        $service = Service::create([
            'department_id' => $dept->id,
            'name' => 'नागरिकता सिफारिस',
            'fee' => 0,
            'status' => true,
        ]);

        $this->application = Application::create([
            'user_id' => $this->citizen->id,
            'service_id' => $service->id,
            'applicant_name' => 'हरि प्रसाद',
            'applicant_email' => 'hari@test.np',
            'status' => 'pending',
        ]);
    }

    public function test_citizen_and_admin_can_fetch_and_post_remarks(): void
    {
        // Citizen posts a remark
        $response = $this->actingAs($this->citizen)
            ->postJson(route('applications.remarks.store', $this->application), [
                'message' => 'मेरो निवेदन कहिले स्वीकृत हुन्छ होला?',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'remark' => [
                    'sender_role' => 'citizen',
                    'message' => 'मेरो निवेदन कहिले स्वीकृत हुन्छ होला?',
                ],
            ]);

        // Admin posts a reply
        $adminResponse = $this->actingAs($this->admin)
            ->postJson(route('applications.remarks.store', $this->application), [
                'message' => 'कागजात रुजु भइरहेको छ, आजै निर्णय हुन्छ।',
            ]);

        $adminResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'remark' => [
                    'sender_role' => 'admin',
                ],
            ]);

        // Fetch remarks
        $fetchResponse = $this->actingAs($this->citizen)
            ->getJson(route('applications.remarks.index', $this->application));

        $fetchResponse->assertStatus(200)
            ->assertJsonCount(2, 'remarks');
    }

    public function test_unauthorized_user_cannot_access_remarks(): void
    {
        $response = $this->actingAs($this->otherCitizen)
            ->getJson(route('applications.remarks.index', $this->application));

        $response->assertStatus(403);
    }
}
