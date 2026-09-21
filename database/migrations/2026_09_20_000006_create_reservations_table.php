<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete(); // pemesan
            $table->foreignId('facility_id')
                ->constrained('facilities')
                ->restrictOnDelete();
            $table->date('reservation_date');
            // start_time & end_time divalidasi di backend: rentang 07.00-20.00
            // dan kelipatan slot 30 menit. Tidak cukup diandalkan dari frontend.
            $table->time('start_time');
            $table->time('end_time');
            $table->text('purpose');
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();

            // Index untuk query pengecekan bentrok jadwal saat approval reservasi.
            // Availability: slot dianggap "occupied" jika ada reservasi berstatus
            // 'pending' ATAU 'approved' pada facility_id + reservation_date yang sama
            // dengan rentang waktu overlap.
            $table->index(['facility_id', 'reservation_date', 'status'], 'idx_reservations_conflict_check');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
