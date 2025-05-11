<?php

declare(strict_types=1);

use App\Models\User;

use function Pest\Laravel\postJson;

beforeEach(function (): void {
    $this->artisan('migrate:fresh');
});

it('can get refresh token with valid token', function (): void {
    $user = User::factory()->create();

    $response = postJson('/api/auth/token/refresh', [], asJwt($user));

    $response
        ->assertOk()
        ->assertJsonStructure([
            'access_token',
        ]);
});

it('fails when not authenticated', function (): void {
    $response = postJson('/api/auth/token/refresh');
    $response->assertUnauthorized();
});
