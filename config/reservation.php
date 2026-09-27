<?php

/**
 * Aturan reservasi (Orang 3). Semua angka aturan bisnis ditaruh di sini
 * supaya tidak ada angka hardcode tersebar di controller/service/view.
 */
return [
    // Jam operasional (format H:i). start_time >= open_time, end_time <= close_time.
    'open_time' => '07:00',
    'close_time' => '20:00',

    // Slot tetap 30 menit: start_time & end_time harus kelipatan ini.
    'slot_minutes' => 30,

    // Reservasi hanya boleh diajukan sampai N hari ke depan (hari ini s/d hari ini + N).
    'max_days_ahead' => 7,

    // Reservasi BARU hanya boleh diajukan minimal N jam sebelum jam mulai (H-1 = 24 jam).
    // Sengaja disamakan dengan cancel_min_hours: kalau sudah terlalu mepet untuk diajukan,
    // otomatis juga berarti sudah terlalu mepet untuk dibatalkan lagi.
    'min_advance_hours' => 24,

    // Pembatalan oleh pengguna hanya boleh sampai N jam sebelum jam mulai (H-1 = 24 jam).
    'cancel_min_hours' => 24,

    // Maksimal reservasi berstatus 'pending' yang boleh dipegang satu pengguna.
    'max_pending_per_user' => 10,
];
