<?php

declare(strict_types=1);

use App\Models\User;

use function Pest\Laravel\postJson;

beforeEach(function (): void {
    $this->artisan('migrate:fresh');
});

it('can logout authenticated user and delete token', function (): void {
    $user = User::factory()->create();

    $response = postJson('/api/auth/logout', [], asJwt($user));

    $response
        ->assertOk()
        ->assertJson([
            'message' => 'Signed out successfully',
        ]);
});

it('fails when not authenticated', function (): void {
    $response = postJson('/api/auth/logout');
    $response->assertUnauthorized();
});
