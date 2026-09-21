<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')
                ->constrained('reports')
                ->cascadeOnDelete(); // foto ikut terhapus jika laporan dihapus
            $table->string('file_path');
            $table->timestamp('uploaded_at')->useCurrent();

            // Catatan: relasi reports <-> report_photos didesain mandatory-mandatory
            // (satu laporan wajib punya minimal 1 foto). Sisi "laporan wajib
            // punya foto" tidak bisa ditegakkan lewat FK/migration, harus dijaga
            // di aplikasi: INSERT reports + INSERT report_photos (minimal 1)
            // dibungkus dalam satu DB transaction, rollback jika tidak ada foto
            // yang berhasil di-attach. Lihat README bagian "Relasi mandatory-mandatory".
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_photos');
    }
};
