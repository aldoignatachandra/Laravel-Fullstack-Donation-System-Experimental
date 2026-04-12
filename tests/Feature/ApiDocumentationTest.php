<?php

use App\Models\User;
use Illuminate\Support\Facades\Artisan;

test('public users can open api documentation ui', function () {
    $response = $this->get('/api/documentation');

    $response->assertOk();
});

test('openapi json can be generated and accessed publicly', function () {
    Artisan::call('l5-swagger:generate');

    $response = $this->get('/docs');

    $response->assertOk();
    $response->assertJsonStructure([
        'openapi',
        'info' => ['title', 'version'],
        'paths',
    ]);
    $response->assertJsonPath('openapi', '3.1.0');
});

test('api user endpoint requires sanctum authentication', function () {
    $response = $this->getJson('/api/user');

    $response->assertUnauthorized();
});

test('authenticated users can fetch their profile from api user endpoint', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->getJson('/api/user');

    $response->assertOk();
    $response->assertJsonPath('email', $user->email);
});

// Campaigns (public)
test('api campaigns endpoint returns paginated json', function () {
    $response = $this->getJson('/api/campaigns');

    $response->assertOk();
});

test('api featured campaigns endpoint returns json', function () {
    $response = $this->getJson('/api/campaigns/featured');

    $response->assertOk();
});

// Categories (public)
test('api categories endpoint returns json', function () {
    $response = $this->getJson('/api/categories');

    $response->assertOk();
});

// Donations (protected)
test('donations index requires auth', function () {
    $response = $this->getJson('/api/donations');

    $response->assertUnauthorized();
});

test('donations summary requires auth', function () {
    $response = $this->getJson('/api/donations/summary');

    $response->assertUnauthorized();
});

test('create donation requires auth', function () {
    $response = $this->postJson('/api/campaigns/test-slug/donations', []);

    $response->assertUnauthorized();
});

// Profile (protected)
test('update profile requires auth', function () {
    $response = $this->putJson('/api/user/profile', []);

    $response->assertUnauthorized();
});

test('update password requires auth', function () {
    $response = $this->putJson('/api/user/password', []);

    $response->assertUnauthorized();
});
