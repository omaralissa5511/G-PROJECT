<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;
    protected $guarded = [];
//    protected $fillable = ['FName','lName','user_id',
//        'address','birth','gender','profile'];
    protected $table = 'profiles';

    protected $hidden = [
        'created_at',
        'updated_at',
        'user_id'
    ];
    public function auctions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Auction::class);
    }
    public function bids(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Bid::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function insurances(){
        return $this->hasMany(Insurance::class);
    }

    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }

    public function HRatings()
    {
        return $this->hasMany(HRating::class);
    }


}
