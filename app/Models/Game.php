<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 
use App\Models\GameVersion;
use App\Models\User;

class Game extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    // Relasi: Game dibuat oleh 1 User (Author)
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    // Relasi: Game punya banyak versi
    public function versions()
    {
        return $this->hasMany(GameVersion::class);
    }

    // Helper: Ambil versi terakhir
    public function latestVersion()
    {
        return $this->hasOne(GameVersion::class)->latestOfMany();
    }
}