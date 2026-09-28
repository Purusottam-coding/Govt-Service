<?php

it('redirects root to login page', function () {
    $response = $this->get('/');

    $response->assertRedirect(route('login'));
});

it('loads login page successfully', function () {
    $response = $this->get(route('login'));

    $response->assertStatus(200);
});
