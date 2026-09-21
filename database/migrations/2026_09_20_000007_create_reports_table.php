<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete(); // pelapor
            $table->foreignId('facility_id')
                ->constrained('facilities')
                ->restrictOnDelete();
            $table->string('category');
            // description boleh kosong selama status masih 'draft'
            $table->text('description')->nullable();
            $table->enum('status', ['draft', 'baru', 'diproses', 'selesai', 'ditolak'])->default('draft');
            $table->text('resolution_note')->nullable();
            $table->dateTime('resolved_at')->nullable(); // diisi saat status -> selesai/ditolak
            $table->timestamps();

            // Index untuk dashboard/antrian laporan petugas.
            $table->index(['facility_id', 'status'], 'idx_reports_queue');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
