<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Department;
use App\Models\Service;
use App\Models\User;
use App\Services\CertificateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificateGenerationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $citizen;
    protected User $otherCitizen;
    protected Department $department;
    protected Service $service;
    protected Application $approvedApplication;
    protected Application $pendingApplication;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->citizen = User::factory()->create(['role' => 'citizen']);
        $this->otherCitizen = User::factory()->create(['role' => 'citizen']);

        $this->department = Department::firstOrCreate(
            ['code' => 'YOJ'],
            [
                'name' => 'योजना तथा पूर्वाधार विकास शाखा',
                'status' => true,
            ]
        );

        $this->service = Service::create([
            'department_id' => $this->department->id,
            'name' => 'घर बाटो सिफारिस',
            'fee' => 500,
            'status' => true,
        ]);

        $this->approvedApplication = Application::create([
            'user_id' => $this->citizen->id,
            'service_id' => $this->service->id,
            'applicant_name' => 'राम प्रसाद रिजाल',
            'applicant_email' => 'ram@test.np',
            'applicant_phone' => '9841000000',
            'applicant_address' => 'बाह्रदशी-३, झापा',
            'status' => 'approved',
            'certificate_number' => 'YOJ909',
            'issued_at' => now(),
        ]);

        $this->pendingApplication = Application::create([
            'user_id' => $this->citizen->id,
            'service_id' => $this->service->id,
            'applicant_name' => 'राम प्रसाद रिजाल',
            'applicant_email' => 'ram@test.np',
            'status' => 'pending',
        ]);
    }

    public function test_citizen_can_view_official_certificate_for_approved_application(): void
    {
        $response = $this->actingAs($this->citizen)->get(
            route('citizen.approved-documents.certificate', $this->approvedApplication)
        );

        $response->assertStatus(200);
        $response->assertSee('बाह्रदशी गाउँपालिका');
        $response->assertSee('गाउँ कार्यपालिकाको कार्यालय');
        $response->assertSee('YOJ909');
        $response->assertSee('राम प्रसाद रिजाल');
        $response->assertSee('घर बाटो सिफारिस');
        $response->assertSee('data:image', false); // QR code data URI
    }

    public function test_citizen_can_download_official_certificate_pdf(): void
    {
        $response = $this->actingAs($this->citizen)->get(
            route('citizen.approved-documents.certificate.pdf', $this->approvedApplication)
        );

        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->headers->get('content-type'), 'application/pdf'));
    }

    public function test_citizen_cannot_view_or_download_unapproved_application_certificate(): void
    {
        // 1. View attempt
        $viewResponse = $this->actingAs($this->citizen)->get(
            route('citizen.approved-documents.certificate', $this->pendingApplication)
        );
        $viewResponse->assertSessionHas('error');

        // 2. Download attempt
        $downloadResponse = $this->actingAs($this->citizen)->get(
            route('citizen.approved-documents.certificate.pdf', $this->pendingApplication)
        );
        $downloadResponse->assertSessionHas('error');
    }

    public function test_citizen_cannot_access_other_citizens_certificate(): void
    {
        $response = $this->actingAs($this->otherCitizen)->get(
            route('citizen.approved-documents.certificate', $this->approvedApplication)
        );

        $response->assertStatus(403);
    }

    public function test_admin_can_view_and_download_any_approved_certificate(): void
    {
        // 1. Admin views certificate
        $viewResponse = $this->actingAs($this->admin)->get(
            route('admin.applications.certificate', $this->approvedApplication)
        );
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('YOJ909');
        $viewResponse->assertSee('राम प्रसाद रिजाल');

        // 2. Admin downloads PDF
        $pdfResponse = $this->actingAs($this->admin)->get(
            route('admin.applications.certificate.pdf', $this->approvedApplication)
        );
        $pdfResponse->assertStatus(200);
        $this->assertTrue(str_contains($pdfResponse->headers->get('content-type'), 'application/pdf'));
    }

    public function test_certificate_service_generates_valid_qr_and_data(): void
    {
        $service = app(CertificateService::class);
        $data = $service->getCertificateData($this->approvedApplication);

        $this->assertEquals('YOJ909', $data['certificateNumber']);
        $this->assertStringContainsString('verify?query=YOJ909', $data['verificationUrl']);
        $this->assertStringStartsWith('data:image', $data['qrCodeDataUri']);
        $this->assertEquals('राम प्रसाद रिजाल', $data['applicantName']);
    }
}
