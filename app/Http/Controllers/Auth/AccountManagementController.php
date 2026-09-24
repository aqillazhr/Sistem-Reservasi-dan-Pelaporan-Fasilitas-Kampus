<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * Modul: Autentikasi, Akun & Integrasi (Orang 1)
 * Halaman: /admin/akun (Manajemen Pengguna)
 */
class AccountManagementController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $pendingUsers = User::where('status', 'pending')
            ->when($search, fn ($q) => $q->where(fn ($q2) => $q2
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
            ))
            ->latest()
            ->get();

        $verifiedUsers = User::where('status', 'verified')
            ->when($search, fn ($q) => $q->where(fn ($q2) => $q2
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
            ))
            ->latest()
            ->get();

        return view('admin.accounts.index', compact('pendingUsers', 'verifiedUsers', 'search'));
    }

    // Halaman "Detail Pengajuan Akun" — dipakai admin buat lihat detail
    // sebelum klik Terima/Tolak.
    public function show(User $user)
    {
        return view('admin.accounts.show', compact('user'));
    }

    public function verify(User $user)
    {
        // Saat status -> verified, account_status WAJIB diisi 'aktif'
        // (lihat CHECK constraint di migration users)
        $user->update([
            'status' => 'verified',
            'account_status' => 'aktif',
        ]);

        return redirect()->route('admin.accounts.index')->with('status', "Akun {$user->name} berhasil diverifikasi.");
    }

    public function reject(User $user)
    {
        $user->update([
            'status' => 'rejected',
            'account_status' => null,
        ]);

        return redirect()->route('admin.accounts.index')->with('status', "Akun {$user->name} ditolak.");
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:8'],
            'role' => ['required', 'in:pengguna,petugas'],
            'user_type' => ['nullable', 'required_if:role,pengguna', 'in:mahasiswa,dosen,staf'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'user_type' => $validated['role'] === 'pengguna' ? $validated['user_type'] : null,
            // Dibuat langsung oleh admin -> otomatis verified & aktif,
            // tidak lewat alur registrasi mandiri.
            'status' => 'verified',
            'account_status' => 'aktif',
        ]);

        return back()->with('status', 'Akun berhasil dibuat.');
    }

    // Form edit data user yang sudah verified (admin bisa ubah nama/email/role).
    public function edit(User $user)
    {
        return view('admin.accounts.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', 'in:admin,petugas,pengguna'],
            'user_type' => ['nullable', 'required_if:role,pengguna', 'in:mahasiswa,dosen,staf'],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'user_type' => $validated['role'] === 'pengguna' ? $validated['user_type'] : null,
        ]);

        return redirect()->route('admin.accounts.index')->with('status', "Data {$user->name} berhasil diperbarui.");
    }

    public function toggleActive(User $user)
    {
        if ($user->status !== 'verified') {
            return back()->withErrors(['user' => 'Akun belum verified, tidak bisa diaktif/nonaktifkan.']);
        }

        $user->account_status = $user->account_status === 'aktif' ? 'nonaktif' : 'aktif';
        $user->save();

        return back()->with('status', "Status akun {$user->name} diubah jadi {$user->account_status}.");
    }
}