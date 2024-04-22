<?php

namespace App\Models\CLUB;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equestrian_club extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'equestrian_clubs';

    protected $hidden = [
        'created_at',
        'updated_at',
        'user_id'
    ];

    public function trainer (){
        return $this->hasMany(Trainer::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class);
    }
    public function clubRates()
    {
        return $this->hasMany(CRating::class);
    }
    public function  images(){
        return $this->hasOne(ClubImage::class);
    }
}
