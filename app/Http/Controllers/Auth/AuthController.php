<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Autentikasi manual (Auth bawaan Laravel, tanpa starter kit).
 * FR-001: login email + password, cek is_active, redirect sesuai role.
 */
class AuthController extends Controller
{
    /** Tampilkan halaman login (desain mengikuti Figma). */
    public function create(): View
    {
        return view('auth.login');
    }

    /** Proses login. */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required'    => 'Email wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // FR-001: akun nonaktif tidak boleh login.
        $user = User::where('email', $credentials['email'])->first();
        if ($user && ! $user->is_active) {
            throw ValidationException::withMessages([
                'email' => 'Akun Anda telah dinonaktifkan. Hubungi Admin.',
            ]);
        }

        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => 'Email atau password salah.',
            ]);
        }

        $request->session()->regenerate();

        // Redirect ke dashboard sesuai role.
        return redirect()->intended(route(Auth::user()->dashboardRoute()));
    }

    /** Logout. */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
