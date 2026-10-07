<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsSuperAdmin
{
    /**
     * Membatasi akses ke fitur Kelola Admin untuk satu akun Super Admin bawaan sistem.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        abort_unless($user, 401);

        if (! $user->isSuperAdmin()) {
            abort(403, 'Hanya Super Admin yang dapat mengelola Kelola Admin.');
        }

        return $next($request);
    }
}