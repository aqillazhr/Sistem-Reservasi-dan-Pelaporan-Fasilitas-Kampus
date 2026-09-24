<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name'           => fake('id_ID')->name(),
            'email'          => fake()->unique()->safeEmail(),
            'password'       => static::$password ??= Hash::make('password'),
            'role'           => 'pengguna',
            'user_type'      => fake()->randomElement(['mahasiswa', 'dosen', 'staf']),
            'status'         => 'verified',
            'account_status' => 'aktif',
        ];
    }

    /** Pengguna biasa (mahasiswa/dosen/staf) yang sudah verified. */
    public function pengguna(): static
    {
        return $this->state(fn () => [
            'role'           => 'pengguna',
            'user_type'      => fake()->randomElement(['mahasiswa', 'dosen', 'staf']),
            'status'         => 'verified',
            'account_status' => 'aktif',
        ]);
    }

    /** Petugas operasional kampus. */
    public function petugas(): static
    {
        return $this->state(fn () => [
            'role'           => 'petugas',
            'user_type'      => null,
            'status'         => 'verified',
            'account_status' => 'aktif',
        ]);
    }

    /** Admin sistem. */
    public function admin(): static
    {
        return $this->state(fn () => [
            'role'           => 'admin',
            'user_type'      => null,
            'status'         => 'verified',
            'account_status' => 'aktif',
        ]);
    }

    /** Akun yang masih menunggu verifikasi. */
    public function pending(): static
    {
        return $this->state(fn () => [
            'status'         => 'pending',
            'account_status' => null,
        ]);
    }

    /** Akun yang ditolak. */
    public function rejected(): static
    {
        return $this->state(fn () => [
            'status'         => 'rejected',
            'account_status' => null,
        ]);
    }

    /** Akun dinonaktifkan. */
    public function nonaktif(): static
    {
        return $this->state(fn () => [
            'status'         => 'verified',
            'account_status' => 'nonaktif',
        ]);
    }

    /** Soft-deleted user. */
    public function deleted(): static
    {
        return $this->state(fn () => ['deleted_at' => now()]);
    }
}
