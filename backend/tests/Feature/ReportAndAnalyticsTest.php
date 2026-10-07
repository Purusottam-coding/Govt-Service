<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Department;
use App\Models\Payment;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportAndAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $citizen;
    protected Department $deptYoj;
    protected Department $deptRaj;
    protected Service $service1;
    protected Service $service2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->citizen = User::factory()->create(['role' => 'citizen']);

        $this->deptYoj = Department::firstOrCreate(
            ['code' => 'YOJ'],
            [
                'name' => 'योजना तथा पूर्वाधार विकास शाखा',
                'status' => true,
            ]
        );

        $this->deptRaj = Department::firstOrCreate(
            ['code' => 'RAJ'],
            [
                'name' => 'राजस्व तथा आर्थिक प्रशासन शाखा',
                'status' => true,
            ]
        );

        $this->service1 = Service::create([
            'department_id' => $this->deptYoj->id,
            'name' => 'घर बाटो सिफारिस',
            'fee' => 500,
            'status' => true,
        ]);

        $this->service2 = Service::create([
            'department_id' => $this->deptRaj->id,
            'name' => 'व्यवसाय कर दर्ता',
            'fee' => 1500,
            'status' => true,
        ]);

        // Create application with completed payment
        $app1 = Application::create([
            'user_id' => $this->citizen->id,
            'service_id' => $this->service1->id,
            'applicant_name' => 'राम प्रसाद',
            'applicant_email' => 'ram@test.np',
            'applicant_phone' => '9841000000',
            'status' => 'approved',
            'certificate_number' => 'YOJ101',
        ]);

        Payment::create([
            'application_id' => $app1->id,
            'amount' => 500,
            'payment_method' => 'online',
            'status' => 'completed',
            'paid_at' => now(),
        ]);

        // Create another application with pending payment
        $app2 = Application::create([
            'user_id' => $this->citizen->id,
            'service_id' => $this->service2->id,
            'applicant_name' => 'सीता शर्मा',
            'applicant_email' => 'sita@test.np',
            'applicant_phone' => '9841000001',
            'status' => 'pending',
        ]);

        Payment::create([
            'application_id' => $app2->id,
            'amount' => 1500,
            'payment_method' => 'cash',
            'status' => 'pending',
        ]);
    }

    public function test_citizen_cannot_access_admin_reports(): void
    {
        $response = $this->actingAs($this->citizen)->get(route('admin.reports.index'));
        $response->assertStatus(403);
    }

    public function test_admin_can_access_reports_and_see_kpis(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.reports.index'));

        $response->assertStatus(200);
        $response->assertSee('शाखागत कार्यसम्पादन तथा राजस्व विश्लेषण');
        $response->assertSee('योजना तथा पूर्वाधार विकास शाखा');
        $response->assertSee('राजस्व तथा आर्थिक प्रशासन शाखा');
        $response->assertSee('500.00'); // Completed revenue
    }

    public function test_admin_can_filter_reports_by_department(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.reports.index', [
            'department_id' => $this->deptYoj->id,
        ]));

        $response->assertStatus(200);
        $response->assertSee('योजना तथा पूर्वाधार विकास शाखा');
    }

    public function test_admin_can_export_applications_csv(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.reports.export-csv', [
            'type' => 'applications',
        ]));

        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->headers->get('content-type'), 'text/csv'));
        $this->assertTrue(str_contains($response->headers->get('content-disposition'), 'barhadashi_report_applications'));

        // Verify CSV content via stream
        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        $this->assertStringContainsString('निवेदन नं.', $content);
        $this->assertStringContainsString('राम प्रसाद', $content);
        $this->assertStringContainsString('घर बाटो सिफारिस', $content);
    }

    public function test_admin_can_export_departments_summary_csv(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.reports.export-csv', [
            'type' => 'departments',
        ]));

        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->headers->get('content-type'), 'text/csv'));
        $this->assertTrue(str_contains($response->headers->get('content-disposition'), 'barhadashi_report_departments'));

        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        $this->assertStringContainsString('शाखाको नाम', $content);
        $this->assertStringContainsString('योजना तथा पूर्वाधार विकास शाखा', $content);
        $this->assertStringContainsString('राजस्व तथा आर्थिक प्रशासन शाखा', $content);
    }
}
