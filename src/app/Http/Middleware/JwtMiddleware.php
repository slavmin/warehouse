<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            JWTAuth::parseToken()->authenticate();
        } catch (TokenBlacklistedException|TokenInvalidException) {
            abort(Response::HTTP_UNAUTHORIZED, 'Access token invalid');
        } catch (TokenExpiredException) {
            abort(Response::HTTP_UNAUTHORIZED, 'Access token expired');
        } catch (JWTException|Exception) {
            abort(Response::HTTP_UNAUTHORIZED, 'Unauthorized');
        }

        return $next($request);
    }
}
