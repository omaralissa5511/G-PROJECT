<?php

namespace App\Models\CLUB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trainer extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'trainers';

    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
        'user_id'
    ];

    public function services()
    {
        return $this->belongsToMany(Service::class);
    }
    public function clubs (){
        return $this->belongsTo(Equestrian_club::class);
    }
    public function trainerRates()
    {
        return $this->hasMany(TRating::class);
    }
}
