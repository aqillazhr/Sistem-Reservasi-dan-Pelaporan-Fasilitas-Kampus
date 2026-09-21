<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('type_id')
                ->constrained('facility_types')
                ->restrictOnDelete();
            $table->foreignId('location_id')
                ->constrained('locations')
                ->restrictOnDelete();
            // Catatan: relasi locations <-> facilities didesain mandatory-mandatory
            // (satu lokasi wajib punya minimal 1 fasilitas). Sisi "lokasi wajib
            // punya anak" tidak bisa ditegakkan lewat FK/migration, harus dijaga
            // di aplikasi (transaction saat insert lokasi baru + larang hapus
            // fasilitas terakhir dari sebuah lokasi). Lihat README bagian
            // "Relasi mandatory-mandatory".
            $table->unsignedInteger('capacity');
            $table->text('description')->nullable();
            $table->enum('status', ['aktif', 'dalam perbaikan', 'nonaktif'])->default('aktif');
            // Soft delete: sama seperti users, penghapusan fasilitas dilakukan
            // lewat deleted_at supaya riwayat reservasi/laporan/foto tetap valid.
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facilities');
    }
};
