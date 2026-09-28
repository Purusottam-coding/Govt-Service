<?php

use App\Models\User;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});

test('admin can authenticate with admin role and is redirected to admin dashboard', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $response = $this->post('/login', [
        'email' => $admin->email,
        'password' => 'password',
        'role' => 'admin',
    ]);

    $this->assertAuthenticatedAs($admin);
    $response->assertRedirect(route('admin.dashboard', absolute: false));
});

test('citizen can authenticate with citizen role and is redirected to citizen dashboard', function () {
    $citizen = User::factory()->create([
        'role' => 'citizen',
    ]);

    $response = $this->post('/login', [
        'email' => $citizen->email,
        'password' => 'password',
        'role' => 'citizen',
    ]);

    $this->assertAuthenticatedAs($citizen);
    $response->assertRedirect(route('citizen.dashboard', absolute: false));
});

test('admin cannot authenticate when citizen role is selected', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $response = $this->post('/login', [
        'email' => $admin->email,
        'password' => 'password',
        'role' => 'citizen',
    ]);

    $this->assertGuest();
    $response->assertSessionHasErrors('email');
});

test('citizen cannot authenticate when admin role is selected', function () {
    $citizen = User::factory()->create([
        'role' => 'citizen',
    ]);

    $response = $this->post('/login', [
        'email' => $citizen->email,
        'password' => 'password',
        'role' => 'admin',
    ]);

    $this->assertGuest();
    $response->assertSessionHasErrors('email');
});

