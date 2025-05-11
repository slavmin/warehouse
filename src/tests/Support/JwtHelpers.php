<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

function asJwt(User $user): array
{
    return ['Authorization' => 'Bearer '.auth('api')->login($user)];
}

function withExpiredToken(User $user): array
{
    $token = JWTAuth::customClaims(['exp' => now()->subHour()->timestamp])->fromUser($user);

    return ['Authorization' => 'Bearer '.$token];
}

function withInvalidToken(): array
{
    return ['Authorization' => 'Bearer '.Str::random(64)];
}

function withMalformedToken(User $user): array
{
    return ['Authorization' => auth()->login($user)];
}

function withTokenForDeletedUser(): array
{
    $user = User::factory()->create();
    $token = auth()->login($user);
    $user->delete();

    return ['Authorization' => 'Bearer '.$token];
}
