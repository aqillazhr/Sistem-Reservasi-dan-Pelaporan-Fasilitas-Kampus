<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            // Penanda apakah lokasi ini tingkat universitas (fasilitas umum kampus)
            // atau tingkat fakultas. Dipakai sebagai toggle di form admin.
            $table->enum('scope_level', ['universitas', 'fakultas'])->default('fakultas');
            $table->string('fakultas')->nullable();
            $table->string('prodi')->nullable();
            $table->string('gedung')->nullable(); // kosong untuk fasilitas outdoor
            $table->string('ruangan')->nullable(); // kosong untuk fasilitas outdoor
        });

        // CHECK: fakultas wajib NULL jika scope_level = 'universitas',
        // dan wajib diisi jika scope_level = 'fakultas'.
        DB::statement("
            ALTER TABLE locations
            ADD CONSTRAINT chk_locations_fakultas
            CHECK (
                (scope_level = 'universitas' AND fakultas IS NULL)
                OR
                (scope_level = 'fakultas' AND fakultas IS NOT NULL)
            )
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
