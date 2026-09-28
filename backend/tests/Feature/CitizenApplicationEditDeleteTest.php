<?php

use App\Enums\PaymentStatus;
use App\Models\Application;
use App\Models\Department;
use App\Models\Payment;
use App\Models\Service;
use App\Models\User;

beforeEach(function () {
    $this->citizen = User::factory()->create([
        'role' => 'citizen',
    ]);

    $this->otherCitizen = User::factory()->create([
        'role' => 'citizen',
    ]);

    $this->department = Department::create([
        'name' => 'परीक्षण विभाग',
        'email' => 'testdept@gov.np',
        'phone' => '01-1234567',
        'status' => true,
    ]);

    $this->service = Service::create([
        'department_id' => $this->department->id,
        'name' => 'परीक्षण सेवा',
        'fee' => 500,
        'processing_days' => 3,
        'status' => true,
    ]);
});

test('citizen can see edit and delete options on applications index before payment', function () {
    $application = Application::create([
        'user_id' => $this->citizen->id,
        'service_id' => $this->service->id,
        'applicant_name' => 'नागरिक नाम',
        'applicant_email' => $this->citizen->email,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->citizen)
        ->get(route('citizen.applications.index'));

    $response->assertStatus(200);
    $response->assertSee(route('citizen.applications.edit', $application));
    $response->assertSee(route('citizen.applications.destroy', $application));
    $response->assertSee('सम्पादन');
    $response->assertSee('हटाउनुहोस्');
});

test('citizen can view edit page before payment', function () {
    $application = Application::create([
        'user_id' => $this->citizen->id,
        'service_id' => $this->service->id,
        'applicant_name' => 'नागरिक नाम',
        'applicant_email' => $this->citizen->email,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->citizen)
        ->get(route('citizen.applications.edit', $application));

    $response->assertStatus(200);
    $response->assertSee($application->applicant_name);
    $response->assertSee('निवेदन सम्पादन गर्नुहोस्');
});

test('citizen can update application before payment', function () {
    $application = Application::create([
        'user_id' => $this->citizen->id,
        'service_id' => $this->service->id,
        'applicant_name' => 'पुरानो नाम',
        'applicant_email' => $this->citizen->email,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->citizen)
        ->put(route('citizen.applications.update', $application), [
            'service_id' => $this->service->id,
            'applicant_name' => 'नयाँ अपडेट गरिएको नाम',
            'applicant_email' => $this->citizen->email,
            'applicant_phone' => '9800000000',
            'applicant_address' => 'काठमाडौं, नेपाल',
        ]);

    $response->assertRedirect(route('citizen.applications.show', $application));

    $this->assertDatabaseHas('applications', [
        'id' => $application->id,
        'applicant_name' => 'नयाँ अपडेट गरिएको नाम',
        'applicant_phone' => '9800000000',
    ]);
});

test('citizen can delete application before payment', function () {
    $application = Application::create([
        'user_id' => $this->citizen->id,
        'service_id' => $this->service->id,
        'applicant_name' => 'हटाउन लागिएको नाम',
        'applicant_email' => $this->citizen->email,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->citizen)
        ->delete(route('citizen.applications.destroy', $application));

    $response->assertRedirect(route('citizen.applications.index'));

    $this->assertDatabaseMissing('applications', [
        'id' => $application->id,
    ]);
});

test('citizen cannot edit or delete another citizens application', function () {
    $application = Application::create([
        'user_id' => $this->otherCitizen->id,
        'service_id' => $this->service->id,
        'applicant_name' => 'अर्को नागरिक',
        'applicant_email' => $this->otherCitizen->email,
        'status' => 'pending',
    ]);

    $editResponse = $this->actingAs($this->citizen)
        ->get(route('citizen.applications.edit', $application));
    $editResponse->assertStatus(403);

    $deleteResponse = $this->actingAs($this->citizen)
        ->delete(route('citizen.applications.destroy', $application));
    $deleteResponse->assertStatus(403);
});

test('citizen cannot edit or delete application after payment has been made', function () {
    $application = Application::create([
        'user_id' => $this->citizen->id,
        'service_id' => $this->service->id,
        'applicant_name' => 'भुक्तानी भइसकेको नागरिक',
        'applicant_email' => $this->citizen->email,
        'status' => 'pending',
    ]);

    Payment::create([
        'application_id' => $application->id,
        'amount' => 500,
        'payment_method' => 'esewa',
        'transaction_id' => 'TXN-TEST-12345',
        'status' => 'completed',
        'paid_at' => now(),
    ]);

    // Refresh application to load payment relation
    $application->refresh();

    expect($application->canBeEdited())->toBeFalse();
    expect($application->canBeDeleted())->toBeFalse();

    $response = $this->actingAs($this->citizen)
        ->get(route('citizen.applications.index'));
    $response->assertStatus(200);
    $response->assertDontSee(route('citizen.applications.edit', $application));

    $editResponse = $this->actingAs($this->citizen)
        ->get(route('citizen.applications.edit', $application));
    $editResponse->assertRedirect(route('citizen.applications.show', $application));

    $deleteResponse = $this->actingAs($this->citizen)
        ->delete(route('citizen.applications.destroy', $application));
    $deleteResponse->assertRedirect(route('citizen.applications.show', $application));

    // Application should still exist
    $this->assertDatabaseHas('applications', [
        'id' => $application->id,
    ]);
});
