<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    // locations tidak punya timestamps (created_at/updated_at) di migration
    public $timestamps = false;

    protected $fillable = [
        'scope_level',
        'fakultas',
        'prodi',
        'gedung',
        'ruangan',
    ];

    public function facilities()
    {
        return $this->hasMany(Facility::class);
    }
}
