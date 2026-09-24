<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    public const STATUS_LABELS = [
        'pending' => 'Menunggu',
        'approved' => 'Aktif',
        'rejected' => 'Ditolak',
        'cancelled' => 'Dibatalkan',
    ];

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

    // ------------------------------------------------------------
    // Scopes
    // ------------------------------------------------------------

    // Slot dianggap occupied kalau statusnya pending ATAU approved
    // (lihat README-database.pdf, Asumsi Perancangan poin 10)
    public function scopeOccupyingSlot(Builder $query): Builder
    {
        return $query->whereIn('status', ['pending', 'approved']);
    }

    // Rentang [start, end) bentrok dengan [start_time, end_time) reservasi ini
    // kalau: existing.start < end DAN existing.end > start.
    // Jadi 08:00-09:00 dan 09:00-10:00 TIDAK bentrok.
    public function scopeOverlapping(Builder $query, string $start, string $end): Builder
    {
        return $query->where('start_time', '<', $end)->where('end_time', '>', $start);
    }

    // ------------------------------------------------------------
    // Accessor / helper tampilan & aturan
    // ------------------------------------------------------------

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function getStartShortAttribute(): string
    {
        return substr((string) $this->start_time, 0, 5);
    }

    public function getEndShortAttribute(): string
    {
        return substr((string) $this->end_time, 0, 5);
    }

    public function startsAt(): Carbon
    {
        return Carbon::parse(
            $this->reservation_date->format('Y-m-d').' '.$this->start_time,
            config('app.timezone')
        );
    }

    public function endsAt(): Carbon
    {
        return Carbon::parse(
            $this->reservation_date->format('Y-m-d').' '.$this->end_time,
            config('app.timezone')
        );
    }

    public function cancelDeadline(): Carbon
    {
        return $this->startsAt()->subHours((int) config('reservation.cancel_min_hours'));
    }

    // Dipakai untuk tombol (abu-abu kalau false) DAN divalidasi ulang di server.
    public function canBeCancelledByOwner(): bool
    {
        return in_array($this->status, ['pending', 'approved'], true)
            && now()->lessThanOrEqualTo($this->cancelDeadline());
    }
}
