<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware pembatas akses berdasarkan role.
 *
 * Cara daftar alias-nya ada di bootstrap/app.php (Laravel 11+), lihat
 * README.md bagian "Setup middleware role" untuk instruksinya.
 *
 * Cara pakai di routes/web.php:
 *   Route::middleware('role:admin')->group(function () { ... });
 *   Route::middleware('role:admin,petugas')->group(function () { ... });
 */
class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Akun yang belum verified atau sudah dinonaktifkan tidak boleh lanjut,
        // meski sudah pernah login sebelumnya (session lama).
        if ($user->status !== 'verified' || $user->account_status === 'nonaktif') {
            auth()->logout();

            return redirect()->route('login')->withErrors([
                'email' => 'Akun kamu belum diverifikasi atau sudah dinonaktifkan.',
            ]);
        }

        if (! in_array($user->role, $roles, true)) {
            abort(403, 'Kamu tidak punya akses ke halaman ini.');
        }

        return $next($request);
    }
}
