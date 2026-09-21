<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password'); // hashed
            $table->enum('role', ['pengguna', 'petugas', 'admin']);
            $table->enum('user_type', ['mahasiswa', 'dosen', 'staf'])->nullable();
            // Status verifikasi. Default 'pending' untuk registrasi mandiri;
            // set 'verified' secara eksplisit di kode saat admin membuat akun petugas/admin.
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
            // Status aktif/nonaktif akun, terpisah dari status verifikasi.
            $table->enum('account_status', ['aktif', 'nonaktif'])->nullable();
            // Soft delete: "penghapusan" user secara nyata dilakukan lewat
            // kolom deleted_at (bukan hard delete), supaya riwayat reservasi,
            // laporan, dan status_logs milik user tetap valid secara FK.
            $table->softDeletes();
            $table->timestamps(); // created_at, updated_at
        });

        // CHECK: account_status hanya boleh terisi ketika status = 'verified'.
        // Efektif di MySQL 8.0.16+. Wajib didobel validasinya di backend
        // jika versi MySQL lebih lama.
        DB::statement("
            ALTER TABLE users
            ADD CONSTRAINT chk_users_account_status
            CHECK (
                (status = 'verified' AND account_status IN ('aktif','nonaktif'))
                OR
                (status <> 'verified' AND account_status IS NULL)
            )
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
