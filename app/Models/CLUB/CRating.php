<?php

namespace App\Models\CLUB;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CRating extends Model
{
    use HasFactory;

    protected $table = 'club_ratings';

    protected $fillable = [
        'club_id',
        'user_id',
        'rating',
        'review',
    ];
    public function clubs()
    {
        return $this->belongsTo(Equestrian_club::class);
    }
    public function users()
    {
        return $this->belongsTo(User::class);
    }
}
