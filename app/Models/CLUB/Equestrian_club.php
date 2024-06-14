<?php

namespace App\Models\CLUB;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equestrian_club extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $fillable = ['name','description','address','profile',
        'day','start','end','license','website','lat','long','user_id'];
    protected $table = 'equestrian_clubs';

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function trainer (){
        return $this->hasMany(Trainer::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }
    public function clubRates()
    {
        return $this->hasMany(CRating::class);
    }
    public function  images(){
        return $this->hasOne(ClubImage::class);
    }

    public function favoritedByUsers()
    {
        return $this->belongsToMany(User::class, 'favorite_clubs');
    }

    public function offerClubs (){
        return $this->hasMany(OfferClub::class);
    }
}
