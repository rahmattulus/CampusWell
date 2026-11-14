<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->isStudent()) {
            if ($user->verification_status === 'pending') {
                return redirect()->route('verification.notice');
            }

            if ($user->verification_status === 'rejected') {
                abort(403, 'Akun mahasiswa tidak disetujui oleh Administrator. Silakan hubungi klinik kampus.');
            }
        }

        return $next($request);
    }
}
