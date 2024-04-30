<?php

namespace App\Models\CLUB;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;
    protected $fillable=[
      'user_id',
      'service_id',
      'trainer_id',
      'status',
      'price'
    ];


    public function service()
    {
        return $this->belongsTo(Service::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }
    public function trainerTimes()
    {
        return $this->hasMany(TrainerTime::class);
    }
}
