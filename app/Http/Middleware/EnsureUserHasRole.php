<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware akses berbasis role (SRS §5 Security, BR-008).
 *
 * Pemakaian di route:  ->middleware('role:admin')
 *                      ->middleware('role:kitchen,barista')
 */
class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // Belum login → dilempar ke login (redirectGuestsTo di bootstrap/app.php).
        if (! $user) {
            return redirect()->route('login');
        }

        // Akun dinonaktifkan di tengah sesi → paksa logout (FR-001).
        if (! $user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => 'Akun Anda telah dinonaktifkan.']);
        }

        // Role tidak sesuai → 403 (role tidak boleh buka halaman role lain).
        if (! in_array($user->role, $roles, true)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
