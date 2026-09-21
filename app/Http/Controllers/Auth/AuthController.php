<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

/**
 * Modul: Autentikasi, Akun & Integrasi (Orang 1)
 * Modul database: Users
 *
 * TODO Orang 1:
 * - register(): registrasi mandiri (mahasiswa/dosen/staf) -> status default 'pending'
 * - login(): tolak login kalau status != 'verified' atau account_status == 'nonaktif'
 * - logout()
 * - redirect setelah login sesuai role (lihat redirectPathForRole())
 */
class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Email atau password salah.',
            ])->onlyInput('email');
        }

        $user = Auth::user();

        if ($user->status !== 'verified') {
            Auth::logout();

            return back()->withErrors([
                'email' => 'Akun kamu masih menunggu verifikasi admin.',
            ]);
        }

        if ($user->account_status === 'nonaktif') {
            Auth::logout();

            return back()->withErrors([
                'email' => 'Akun kamu sudah dinonaktifkan. Hubungi admin.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended($this->redirectPathForRole($user->role));
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'user_type' => ['required', 'in:mahasiswa,dosen,staf'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'pengguna',
            'user_type' => $validated['user_type'],
            'status' => 'pending', // wajib diverifikasi admin dulu
            'account_status' => null, // NULL selama belum verified (lihat CHECK constraint)
        ]);

        return redirect()->route('login')->with(
            'status',
            'Registrasi berhasil. Akun kamu menunggu verifikasi admin sebelum bisa dipakai login.'
        );
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    protected function redirectPathForRole(string $role): string
    {
        return match ($role) {
            'admin' => route('admin.dashboard'),
            'petugas' => route('petugas.dashboard'),
            default => route('pengguna.dashboard'),
        };
    }
}
