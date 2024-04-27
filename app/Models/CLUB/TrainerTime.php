<?php

namespace App\Models\CLUB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class TrainerTime extends Model
{
    use HasFactory;
    protected $fillable = [
        'trainer_id',
        'date',
        'start_time',
        'end_time',
        'is_available',
    ];

    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }
}
