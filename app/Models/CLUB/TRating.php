<?php

namespace App\Models\CLUB;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TRating extends Model
{
    use HasFactory;

    protected $table = 'trainer_ratings';

    protected $fillable = [
        'club_id',
        'user_id',
        'rating',
        'review',
    ];
    public function users()
    {
        return $this->belongsTo(User::class);
    }
    public function trainers()
    {
        return $this->belongsTo(Trainer::class);
    }
}
