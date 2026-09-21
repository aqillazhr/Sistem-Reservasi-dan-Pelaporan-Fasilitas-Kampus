<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusLog extends Model
{
    use HasFactory;

    // Tidak ada updated_at, log bersifat immutable (lihat migration)
    const UPDATED_AT = null;

    protected $fillable = [
        'reservation_id',
        'report_id',
        'facility_id',
        'changed_by_user_id',
        'old_status',
        'new_status',
        'note',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }
}
