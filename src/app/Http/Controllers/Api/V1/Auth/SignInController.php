<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SignInRequest;
use App\Http\Resources\Token\AccessTokenResource;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SignInController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(SignInRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        $user = User::query()->where(User::getIdentityKey(), data_get($credentials, User::getIdentityKey()))->firstOrFail();

        if ($token = auth('api')->attempt($credentials)) {
            $mergedRequest = $request->merge(['token' => $token]);

            return response()->json([
                ...(new AccessTokenResource($mergedRequest))->resolve(),
                'user' => (new UserResource($user)),
            ]);
        }

        return response()->json(['message' => 'SignIn failed'], Response::HTTP_UNAUTHORIZED);
    }
}
