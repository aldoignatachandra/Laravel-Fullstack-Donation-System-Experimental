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
    $response->assertJsonPath('paths./api/webhook/midtrans.post.operationId', 'handleMidtransWebhookCallback');
    $response->assertJsonPath('paths./api/user.get.operationId', 'getCurrentAuthenticatedUser');
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
