<?php

namespace Tests\Feature;

use App\Enums\ApplicationStatus;
use App\Enums\PaymentStatus;
use App\Models\Application;
use App\Models\Department;
use App\Models\Payment;
use App\Models\Service;
use App\Models\User;
use App\Notifications\PaymentVerifiedNotification;
use App\Services\EsewaPaymentService;
use App\Services\KhaltiPaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class DigitalPaymentGatewayTest extends TestCase
{
    use RefreshDatabase;

    protected User $citizen;
    protected User $otherCitizen;
    protected User $admin;
    protected Department $department;
    protected Service $service;
    protected Application $application;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);

        $this->citizen = User::factory()->create([
            'role' => 'citizen',
            'name' => 'सीता देवी शर्मा',
            'email' => 'sita@barhadashi.gov.np',
        ]);

        $this->otherCitizen = User::factory()->create([
            'role' => 'citizen',
            'name' => 'हरी प्रसाद अधिकारी',
            'email' => 'hari@barhadashi.gov.np',
        ]);

        $this->department = Department::firstOrCreate(
            ['code' => 'YOJ'],
            ['name' => 'योजना तथा पूर्वाधार विकास शाखा', 'status' => true]
        );

        $this->service = Service::create([
            'department_id' => $this->department->id,
            'name' => 'घर बाटो सिफारिस',
            'fee' => 750.00,
            'status' => true,
        ]);

        $this->application = Application::create([
            'user_id' => $this->citizen->id,
            'service_id' => $this->service->id,
            'application_number' => 'APP-2083-TEST01',
            'applicant_name' => 'सीता देवी शर्मा',
            'applicant_email' => 'sita@barhadashi.gov.np',
            'applicant_phone' => '9841234567',
            'applicant_address' => 'बाह्रदशी-३, झापा',
            'status' => ApplicationStatus::PENDING->value,
        ]);
    }

    public function test_citizen_can_view_payment_page_with_esewa_and_khalti_options(): void
    {
        $response = $this->actingAs($this->citizen)
            ->get(route('citizen.payments.create', $this->application));

        $response->assertStatus(200);
        $response->assertSee('सरकारी सेवा आवेदन भुक्तानी');
        $response->assertSee('eSewa ePay');
        $response->assertSee('Khalti ePayment');
        $response->assertSee(number_format(750, 2));
    }

    public function test_citizen_can_initiate_esewa_payment_and_receive_auto_redirect_form(): void
    {
        $response = $this->actingAs($this->citizen)
            ->get(route('citizen.payments.esewa.initiate', $this->application));

        $response->assertStatus(200);
        $response->assertViewIs('citizen.payments.esewa_redirect');
        $response->assertSee('eSewa ePay सुरक्षित गेटवे');
        $response->assertSee('rc-epay.esewa.com.np');
        $response->assertSee('signature');
        $response->assertSee('750.00');

        $this->assertDatabaseHas('payments', [
            'application_id' => $this->application->id,
            'payment_method' => 'esewa',
            'status' => PaymentStatus::PENDING->value,
            'amount' => 750.00,
        ]);
    }

    public function test_esewa_success_callback_marks_payment_completed_and_notifies_citizen(): void
    {
        Notification::fake();

        // Simulate eSewa pending payment record
        Payment::create([
            'application_id' => $this->application->id,
            'amount' => 750.00,
            'payment_method' => 'esewa',
            'transaction_id' => 'ESEWA-INIT-12345',
            'status' => PaymentStatus::PENDING->value,
        ]);

        /** @var EsewaPaymentService $esewaService */
        $esewaService = app(EsewaPaymentService::class);
        $uuid = 'APP-' . $this->application->id . '-12345';
        $signature = $esewaService->generateSignature('750.00', $uuid, 'EPAYTEST');

        $callbackData = [
            'transaction_code' => 'TXN-ESEWA-9988',
            'status' => 'COMPLETE',
            'total_amount' => '750.00',
            'transaction_uuid' => $uuid,
            'product_code' => 'EPAYTEST',
            'signature' => $signature,
        ];

        $encodedData = base64_encode(json_encode($callbackData));

        $response = $this->actingAs($this->citizen)
            ->get(route('citizen.payments.esewa.success', [
                'application' => $this->application,
                'data' => $encodedData,
            ]));

        $response->assertRedirect(route('citizen.payments.receipt', $this->application));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('payments', [
            'application_id' => $this->application->id,
            'status' => PaymentStatus::COMPLETED->value,
            'payment_method' => 'esewa',
            'transaction_id' => 'TXN-ESEWA-9988',
        ]);

        $this->assertEquals(
            ApplicationStatus::UNDER_REVIEW->value,
            $this->application->fresh()->status
        );

        Notification::assertSentTo($this->citizen, PaymentVerifiedNotification::class);
    }

    public function test_esewa_failed_callback_redirects_to_payment_screen(): void
    {
        $response = $this->actingAs($this->citizen)
            ->get(route('citizen.payments.esewa.failed', $this->application));

        $response->assertRedirect(route('citizen.payments.create', $this->application));
        $response->assertSessionHas('error');
    }

    public function test_citizen_can_initiate_khalti_payment(): void
    {
        $response = $this->actingAs($this->citizen)
            ->get(route('citizen.payments.khalti.initiate', $this->application));

        // Either redirects to Khalti payment URL or fallback simulation
        $response->assertStatus(302);

        $this->assertDatabaseHas('payments', [
            'application_id' => $this->application->id,
            'payment_method' => 'khalti',
            'status' => PaymentStatus::PENDING->value,
            'amount' => 750.00,
        ]);
    }

    public function test_khalti_callback_marks_payment_completed_and_notifies_citizen(): void
    {
        Notification::fake();

        Payment::create([
            'application_id' => $this->application->id,
            'amount' => 750.00,
            'payment_method' => 'khalti',
            'transaction_id' => 'KHL-INIT-5566',
            'status' => PaymentStatus::PENDING->value,
        ]);

        $response = $this->actingAs($this->citizen)
            ->get(route('citizen.payments.khalti.callback', [
                'application' => $this->application,
                'pidx' => 'KHL-SIM-TESTPIDX123',
                'status' => 'Completed',
            ]));

        $response->assertRedirect(route('citizen.payments.receipt', $this->application));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('payments', [
            'application_id' => $this->application->id,
            'status' => PaymentStatus::COMPLETED->value,
            'payment_method' => 'khalti',
            'transaction_id' => 'KHL-SIM-TESTPIDX123',
        ]);

        $this->assertEquals(
            ApplicationStatus::UNDER_REVIEW->value,
            $this->application->fresh()->status
        );

        Notification::assertSentTo($this->citizen, PaymentVerifiedNotification::class);
    }

    public function test_unauthorized_citizen_cannot_initiate_another_users_payment(): void
    {
        $response = $this->actingAs($this->otherCitizen)
            ->get(route('citizen.payments.esewa.initiate', $this->application));

        $response->assertStatus(403);

        $responseKhalti = $this->actingAs($this->otherCitizen)
            ->get(route('citizen.payments.khalti.initiate', $this->application));

        $responseKhalti->assertStatus(403);
    }
}
