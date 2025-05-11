<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\Token\AccessTokenResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class RefreshTokenController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $newToken = JWTAuth::parseToken()->refresh();

            $mergedRequest = $request->merge(['token' => $newToken]);

            return response()->json(new AccessTokenResource($mergedRequest));
        } catch (JWTException) {
            //
        }

        return response()->json(['message' => 'Token refresh failed'], Response::HTTP_UNAUTHORIZED);
    }
}
