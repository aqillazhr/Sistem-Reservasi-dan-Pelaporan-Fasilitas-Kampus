<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'facility_id',
        'reservation_date',
        'start_time',
        'end_time',
        'purpose',
        'status',
        'cancellation_reason',
    ];

    protected function casts(): array
    {
        return [
            'reservation_date' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function statusLogs()
    {
        return $this->hasMany(StatusLog::class);
    }

    // Slot dianggap occupied kalau statusnya pending ATAU approved
    // (lihat README-database.pdf, Asumsi Perancangan poin 10)
    public function scopeOccupyingSlot($query)
    {
        return $query->whereIn('status', ['pending', 'approved']);
    }
}
