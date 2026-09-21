<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('status_logs', function (Blueprint $table) {
            $table->id();

            // 3 FK nullable yang saling eksklusif: hanya salah satu yang terisi
            // per baris, tergantung log ini milik reservasi, laporan, atau fasilitas.
            $table->foreignId('reservation_id')
                ->nullable()
                ->constrained('reservations')
                ->restrictOnDelete();
            $table->foreignId('report_id')
                ->nullable()
                ->constrained('reports')
                ->restrictOnDelete();
            $table->foreignId('facility_id')
                ->nullable()
                ->constrained('facilities')
                ->restrictOnDelete();

            $table->foreignId('changed_by_user_id')
                ->constrained('users')
                ->restrictOnDelete(); // petugas/admin yang mengubah status

            $table->string('old_status');
            $table->string('new_status');
            $table->text('note')->nullable();
            $table->timestamp('created_at')->useCurrent(); // tidak ada updated_at, log bersifat immutable
        });

        // CHECK: tepat SATU dari reservation_id/report_id/facility_id yang terisi
        // per baris (mencegah baris log yang salah kaitan / ambigu).
        // Efektif di MySQL 8.0.16+. Validasi ulang di backend jika versi lebih lama.
        DB::statement("
            ALTER TABLE status_logs
            ADD CONSTRAINT chk_status_logs_single_entity
            CHECK (
                (
                    (reservation_id IS NOT NULL) +
                    (report_id IS NOT NULL) +
                    (facility_id IS NOT NULL)
                ) = 1
            )
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('status_logs');
    }
};
