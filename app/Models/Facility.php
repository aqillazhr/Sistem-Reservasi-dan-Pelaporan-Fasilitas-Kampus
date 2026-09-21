<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'type_id',
        'location_id',
        'capacity',
        'description',
        'status',
    ];

    // ------------------------------------------------------------
    // Relasi
    // ------------------------------------------------------------

    public function type()
    {
        return $this->belongsTo(FacilityType::class, 'type_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function photos()
    {
        return $this->hasMany(FacilityPhoto::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function statusLogs()
    {
        return $this->hasMany(StatusLog::class);
    }

    // ------------------------------------------------------------
    // Milik Orang 2 (rumah dari tabel facilities).
    // Orang 4 WAJIB memanggil method ini untuk ubah status jadi
    // 'dalam perbaikan' / balik ke 'aktif' — jangan bikin logic
    // status terpisah, supaya tidak ada dua sumber kebenaran.
    // ------------------------------------------------------------
    public function updateFacilityStatus(string $newStatus, ?int $changedByUserId = null, ?string $note = null): void
    {
        $oldStatus = $this->status;

        $this->status = $newStatus;
        $this->save();

        if ($changedByUserId) {
            StatusLog::create([
                'facility_id' => $this->id,
                'changed_by_user_id' => $changedByUserId,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'note' => $note,
            ]);
        }
    }
}
