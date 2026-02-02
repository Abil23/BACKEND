<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Relasi ke User (Pemilik Skor)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Game
    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}