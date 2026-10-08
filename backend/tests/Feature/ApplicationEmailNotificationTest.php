<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\ApplicationDocument;
use App\Models\Department;
use App\Models\Payment;
use App\Models\Service;
use App\Models\User;
use App\Notifications\ApplicationStatusUpdatedNotification;
use App\Notifications\ApplicationSubmittedNotification;
use App\Notifications\DocumentReplacementRequestedNotification;
use App\Notifications\PaymentVerifiedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ApplicationEmailNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $citizen;
    protected Department $department;
    protected Service $paidService;
    protected Service $freeService;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->citizen = User::factory()->create([
            'role' => 'citizen',
            'email' => 'citizen@barhadashi.test',
            'name' => 'राम बहादुर खड्का',
        ]);

        $this->department = Department::firstOrCreate(
            ['code' => 'YOJ'],
            [
                'name' => 'योजना तथा पूर्वाधार विकास शाखा',
                'status' => true,
            ]
        );

        $this->paidService = Service::create([
            'department_id' => $this->department->id,
            'name' => 'घर बाटो सिफारिस',
            'fee' => 500,
            'status' => true,
        ]);

        $this->freeService = Service::create([
            'department_id' => $this->department->id,
            'name' => 'नागरिकता सिफारिस (निःशुल्क)',
            'fee' => 0,
            'status' => true,
        ]);
    }

    public function test_citizen_does_not_receive_email_for_paid_service_until_payment_verified(): void
    {
        Notification::fake();

        $response = $this->actingAs($this->citizen)->post(route('citizen.applications.store'), [
            'service_id' => $this->paidService->id,
            'applicant_name' => $this->citizen->name,
            'applicant_email' => $this->citizen->email,
            'applicant_phone' => '9841234567',
            'applicant_address' => 'बाह्रदशी-१, झापा',
            'documents' => [
                UploadedFile::fake()->create('citizenship.pdf', 500, 'application/pdf'),
            ],
            'document_names' => [
                'नागरिकता प्रमाणपत्र',
            ],
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        // Confirmation email should NOT be sent yet because payment is required and not verified
        Notification::assertNotSentTo($this->citizen, ApplicationSubmittedNotification::class);
    }

    public function test_citizen_receives_confirmation_email_for_free_service_immediately(): void
    {
        Notification::fake();

        $response = $this->actingAs($this->citizen)->post(route('citizen.applications.store'), [
            'service_id' => $this->freeService->id,
            'applicant_name' => $this->citizen->name,
            'applicant_email' => $this->citizen->email,
            'applicant_phone' => '9841234567',
            'applicant_address' => 'बाह्रदशी-१, झापा',
            'documents' => [
                UploadedFile::fake()->create('citizenship.pdf', 500, 'application/pdf'),
            ],
            'document_names' => [
                'नागरिकता प्रमाणपत्र',
            ],
        ]);

        $response->assertSessionHasNoErrors();

        // Confirmation email should be sent for free service immediately
        Notification::assertSentTo(
            $this->citizen,
            ApplicationSubmittedNotification::class,
            function (ApplicationSubmittedNotification $notification) {
                $mail = $notification->toMail($this->citizen);
                $this->assertStringContainsString('निवेदन दर्ता', $mail->subject);
                return $notification->application->applicant_email === 'citizen@barhadashi.test';
            }
        );
    }

    public function test_citizen_receives_payment_verified_email_when_admin_verifies_payment(): void
    {
        Notification::fake();

        $application = Application::create([
            'user_id' => $this->citizen->id,
            'service_id' => $this->paidService->id,
            'applicant_name' => $this->citizen->name,
            'applicant_email' => $this->citizen->email,
            'applicant_phone' => '9841234567',
            'status' => 'pending',
        ]);

        $payment = Payment::create([
            'application_id' => $application->id,
            'amount' => 500,
            'payment_method' => 'esewa',
            'transaction_id' => 'TXN-TEST123',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->patch(
            route('admin.applications.payments.verify', $application)
        );

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('admin.applications.show', $application));

        $payment->refresh();
        $this->assertEquals('completed', $payment->status);

        // Verification email sent to citizen
        Notification::assertSentTo(
            $this->citizen,
            PaymentVerifiedNotification::class,
            function (PaymentVerifiedNotification $notification) use ($application) {
                $mail = $notification->toMail($this->citizen);
                $this->assertStringContainsString('भुक्तानी प्रमाणीकरण', $mail->subject);
                return $notification->application->id === $application->id;
            }
        );
    }

    public function test_citizen_receives_approval_email_when_admin_approves_application(): void
    {
        Notification::fake();

        $application = Application::create([
            'user_id' => $this->citizen->id,
            'service_id' => $this->paidService->id,
            'applicant_name' => $this->citizen->name,
            'applicant_email' => $this->citizen->email,
            'applicant_phone' => '9841234567',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->patch(
            route('admin.applications.status', $application),
            [
                'status' => 'approved',
                'admin_remarks' => 'सबै कागजातहरू रीतपूर्वक पेश भएकाले स्वीकृत गरिएको छ।',
                'approved_document_name' => 'घर बाटो सिफारिस प्रमाणपत्र',
                'approved_document' => UploadedFile::fake()->create('approval.pdf', 300, 'application/pdf'),
                'certificate_number' => 'YOJ701',
            ]
        );

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('admin.applications.show', $application));

        $application->refresh();
        $this->assertEquals('approved', $application->status);
        $this->assertEquals('YOJ701', $application->certificate_number);

        // Verify approval email sent to citizen
        Notification::assertSentTo(
            $this->citizen,
            ApplicationStatusUpdatedNotification::class,
            function (ApplicationStatusUpdatedNotification $notification) {
                $mail = $notification->toMail($this->citizen);
                $this->assertStringContainsString('स्वीकृत', $mail->subject);
                $this->assertStringContainsString('YOJ701', $mail->subject);
                return $notification->application->status === 'approved';
            }
        );
    }

    public function test_citizen_receives_rejection_email_when_admin_rejects_application(): void
    {
        Notification::fake();

        $application = Application::create([
            'user_id' => $this->citizen->id,
            'service_id' => $this->paidService->id,
            'applicant_name' => $this->citizen->name,
            'applicant_email' => $this->citizen->email,
            'applicant_phone' => '9841234567',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->patch(
            route('admin.applications.status', $application),
            [
                'status' => 'rejected',
                'admin_remarks' => 'जग्गाधनी प्रमाणपुर्जा अस्पष्ट भएकोले निवेदन अस्वीकृत गरियो।',
            ]
        );

        $response->assertSessionHasNoErrors();

        $application->refresh();
        $this->assertEquals('rejected', $application->status);

        // Verify rejection email sent to citizen
        Notification::assertSentTo(
            $this->citizen,
            ApplicationStatusUpdatedNotification::class,
            function (ApplicationStatusUpdatedNotification $notification) {
                $mail = $notification->toMail($this->citizen);
                $this->assertStringContainsString('अस्वीकृत', $mail->subject);
                return $notification->application->status === 'rejected';
            }
        );
    }

    public function test_citizen_receives_email_when_admin_requests_document_replacement(): void
    {
        Notification::fake();

        $application = Application::create([
            'user_id' => $this->citizen->id,
            'service_id' => $this->paidService->id,
            'applicant_name' => $this->citizen->name,
            'applicant_email' => $this->citizen->email,
            'applicant_phone' => '9841234567',
            'status' => 'pending',
        ]);

        $document = ApplicationDocument::create([
            'application_id' => $application->id,
            'document_name' => 'नागरिकताको प्रतिलिपि',
            'file_path' => 'documents/test.pdf',
        ]);

        $response = $this->actingAs($this->admin)->post(
            route('admin.applications.documents.request-replacement', [$application, $document]),
            [
                'admin_feedback' => 'नागरिकताको पछाडिको भाग स्पष्ट देखिएन, कृपया पुनः स्क्यान गरेर पठाउनुहोस्।',
            ]
        );

        $response->assertSessionHasNoErrors();

        Notification::assertSentTo(
            $this->citizen,
            DocumentReplacementRequestedNotification::class,
            function (DocumentReplacementRequestedNotification $notification) {
                $mail = $notification->toMail($this->citizen);
                $this->assertStringContainsString('पुनः अपलोड', $mail->subject);
                return $notification->document->document_name === 'नागरिकताको प्रतिलिपि';
            }
        );
    }
}
