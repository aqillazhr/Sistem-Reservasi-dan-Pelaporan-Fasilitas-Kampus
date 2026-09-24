<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * Modul: Profil (Orang 1)
 * Bisa diakses semua role yang sudah login (admin, petugas, pengguna).
 */
class ProfileController extends Controller
{
    public function show(Request $request)
    {
        return view('profile.show', ['user' => $request->user()]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', 'min:8'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        // Password baru bersifat opsional, kalau field-nya dikosongkan
        // berarti user nggak mau ganti password.
        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return back()->with('status', 'Profil berhasil diperbarui.');
    }
}