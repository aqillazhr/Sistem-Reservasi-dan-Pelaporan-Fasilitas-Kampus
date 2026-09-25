<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;


class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        $initials = collect(explode(' ', $user->name))
            ->map(fn ($w) => mb_substr($w, 0, 1))
            ->take(2)
            ->implode('');

        return view('profile.show', ['user' => $user, 'initials' => $initials]);
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

        // Password baru bersifat opsional, kalau fieldnya dikosongin
        // berarti user ga mau ganti password
        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return back()->with('status', 'Profil berhasil diperbarui.');
    }
}