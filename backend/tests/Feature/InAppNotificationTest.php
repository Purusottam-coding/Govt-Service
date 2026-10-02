<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\ApplicationDocument;
use App\Models\Department;
use App\Models\Service;
use App\Models\User;
use App\Notifications\PortalNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InAppNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $citizen;
    protected User $otherCitizen;
    protected Department $department;
    protected Service $service;
    protected Application $application;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->citizen = User::factory()->create(['role' => 'citizen']);
        $this->otherCitizen = User::factory()->create(['role' => 'citizen']);

        $this->department = Department::firstOrCreate(
            ['code' => 'YOJ'],
            [
                'name' => 'योजना शाखा',
                'status' => true,
            ]
        );

        $this->service = Service::create([
            'department_id' => $this->department->id,
            'name' => 'घर बाटो सिफारिस',
            'fee' => 500,
            'status' => true,
        ]);

        $this->application = Application::create([
            'user_id' => $this->citizen->id,
            'service_id' => $this->service->id,
            'applicant_name' => 'सीता शर्मा',
            'applicant_email' => 'sita@test.np',
            'status' => 'pending',
        ]);
    }

    public function test_admin_status_update_triggers_citizen_notification(): void
    {
        $this->assertEquals(0, $this->citizen->unreadNotifications()->count());

        $file = UploadedFile::fake()->create('approved_certificate.pdf', 100, 'application/pdf');

        $response = $this->actingAs($this->admin)->patch(
            route('admin.applications.status', $this->application),
            [
                'status' => 'approved',
                'admin_remarks' => 'सबै कागजात रुजु भयो र स्वीकृत गरियो।',
                'approved_document_name' => 'घर बाटो आधिकारिक प्रमाणपत्र',
                'approved_document' => $file,
            ]
        );

        $response->assertSessionHasNoErrors();

        $this->citizen->refresh();
        $this->assertEquals(1, $this->citizen->unreadNotifications()->count());

        $notification = $this->citizen->unreadNotifications()->first();
        $this->assertEquals('निवेदन स्थिति अद्यावधिक', $notification->data['title']);
        $this->assertStringContainsString('स्वीकृत', $notification->data['message']);
        $this->assertEquals('success', $notification->data['color']);
    }

    public function test_admin_replacement_request_triggers_citizen_notification(): void
    {
        $doc = ApplicationDocument::create([
            'application_id' => $this->application->id,
            'document_name' => 'नागरिकता प्रमाणपत्र',
            'file_path' => 'docs/test.pdf',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->post(
            route('admin.applications.documents.request-replacement', [$this->application, $doc]),
            [
                'admin_feedback' => 'कागजात स्पष्ट भएन, कृपया रंगिन स्क्यान प्रति पठाउनुहोस्।',
            ]
        );

        $response->assertSessionHasNoErrors();

        $this->citizen->refresh();
        $this->assertEquals(1, $this->citizen->unreadNotifications()->count());

        $notification = $this->citizen->unreadNotifications()->first();
        $this->assertEquals('कागजात पुनः पेश गर्न अनुरोध', $notification->data['title']);
        $this->assertStringContainsString('रंगिन स्क्यान', $notification->data['message']);
        $this->assertEquals('warning', $notification->data['color']);
    }

    public function test_citizen_new_application_triggers_admin_notification(): void
    {
        $this->assertEquals(0, $this->admin->unreadNotifications()->count());

        $response = $this->actingAs($this->citizen)->post(
            route('citizen.applications.store'),
            [
                'service_id' => $this->service->id,
                'applicant_name' => 'नवीन श्रेष्ठ',
                'applicant_email' => 'navin@test.np',
                'applicant_phone' => '9800000000',
                'applicant_address' => 'बाह्रदशी-३',
            ]
        );

        $response->assertSessionHasNoErrors();

        $this->admin->refresh();
        $this->assertEquals(1, $this->admin->unreadNotifications()->count());

        $notification = $this->admin->unreadNotifications()->first();
        $this->assertEquals('नयाँ सेवा निवेदन दर्ता', $notification->data['title']);
        $this->assertStringContainsString('नवीन श्रेष्ठ', $notification->data['message']);
    }

    public function test_citizen_payment_upload_triggers_admin_notification(): void
    {
        $statement = UploadedFile::fake()->image('statement.png');

        $response = $this->actingAs($this->citizen)->post(
            route('citizen.payments.store', $this->application),
            [
                'payment_method' => 'esewa',
                'payment_statement' => $statement,
            ]
        );

        $response->assertSessionHasNoErrors();

        $this->admin->refresh();
        $this->assertEquals(1, $this->admin->unreadNotifications()->count());

        $notification = $this->admin->unreadNotifications()->first();
        $this->assertEquals('नयाँ भुक्तानी प्रमाण पेश', $notification->data['title']);
        $this->assertStringContainsString($this->application->application_number, $notification->data['message']);
    }

    public function test_remark_triggers_notification_for_recipient(): void
    {
        // 1. Citizen posts remark -> Admin gets notification
        $this->actingAs($this->citizen)->postJson(
            route('applications.remarks.store', $this->application),
            ['message' => 'प्रशासक ज्यू, मेरो निवेदन कहिलेसम्म स्वीकृत हुन्छ?']
        )->assertOk();

        $this->admin->refresh();
        $this->assertEquals(1, $this->admin->unreadNotifications()->count());
        $adminNotification = $this->admin->unreadNotifications()->first();
        $this->assertEquals('निवेदकबाट नयाँ सन्देश', $adminNotification->data['title']);

        // 2. Admin posts remark -> Citizen gets notification
        $this->actingAs($this->admin)->postJson(
            route('applications.remarks.store', $this->application),
            ['message' => 'कागजात रुजु हुँदैछ, छिट्टै स्वीकृत हुनेछ।']
        )->assertOk();

        $this->citizen->refresh();
        $this->assertEquals(1, $this->citizen->unreadNotifications()->count());
        $citizenNotification = $this->citizen->unreadNotifications()->first();
        $this->assertEquals('नयाँ सन्देश प्राप्त भयो', $citizenNotification->data['title']);
    }

    public function test_user_can_fetch_unread_count_and_dropdown_and_mark_all_read(): void
    {
        $this->citizen->notify(new PortalNotification('परीक्षण सूचना १', 'सन्देश १'));
        $this->citizen->notify(new PortalNotification('परीक्षण सूचना २', 'सन्देश २'));

        $this->assertEquals(2, $this->citizen->unreadNotifications()->count());

        // 1. Fetch unread count JSON
        $countResponse = $this->actingAs($this->citizen)
            ->getJson(route('notifications.unread-count'));
        $countResponse->assertOk()->assertJson(['unread_count' => 2]);

        // 2. Fetch dropdown JSON
        $dropdownResponse = $this->actingAs($this->citizen)
            ->getJson(route('notifications.dropdown'));
        $dropdownResponse->assertOk()
            ->assertJsonStructure(['unread_count', 'notifications' => [['id', 'title', 'message', 'read']]]);

        // 3. Mark all read
        $markAllResponse = $this->actingAs($this->citizen)
            ->postJson(route('notifications.mark-all-read'));
        $markAllResponse->assertOk()->assertJson(['success' => true, 'unread_count' => 0]);

        $this->citizen->refresh();
        $this->assertEquals(0, $this->citizen->unreadNotifications()->count());
    }

    public function test_user_cannot_access_or_modify_other_user_notifications(): void
    {
        $this->citizen->notify(new PortalNotification('गोप्य सूचना', 'नागरिकको मात्र सन्देश'));
        $notification = $this->citizen->notifications()->first();

        // Other citizen tries to mark as read
        $response = $this->actingAs($this->otherCitizen)
            ->get(route('notifications.read', $notification->id));

        $response->assertStatus(404);

        // Original citizen can mark as read
        $validResponse = $this->actingAs($this->citizen)
            ->get(route('notifications.read', $notification->id));
        $validResponse->assertStatus(302);

        $notification->refresh();
        $this->assertNotNull($notification->read_at);
    }
}
