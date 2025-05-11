<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Auth;

use App\Actions\User\CreateUserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SignUpRequest;
use App\Http\Resources\Token\AccessTokenResource;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class SignUpController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(SignUpRequest $request): JsonResponse
    {
        $user = CreateUserAction::fromArray($request->all());

        if ($token = JWTAuth::fromUser($user)) {
            $mergedRequest = $request->merge(['token' => $token]);

            return response()->json([
                ...(new AccessTokenResource($mergedRequest))->resolve(),
                'user' => (new UserResource($user)),
            ], Response::HTTP_CREATED);
        }

        return response()->json(['message' => 'SignUp failed'], Response::HTTP_UNAUTHORIZED);
    }
}
