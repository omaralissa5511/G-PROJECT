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
        'trainer_id',
        'user_id',
        'rating',
        'review',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }
}
