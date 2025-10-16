<?php declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\RoleEnum;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        if (! RoleEnum::hasRole($roles)) {
            abort(Response::HTTP_FORBIDDEN, 'User does not have the right roles.');
        }

        return $next($request);
    }
}
