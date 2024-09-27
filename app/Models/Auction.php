<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auction extends Model
{
    use HasFactory;
    protected $table = 'auctions';
    protected $fillable = ['initialPrice','end','begin','limit','winner_id',
                   'description','profile_id','status'];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    public function bids(){
        return $this->hasMany(Bid::class);
    }
    public function horses(){
        return $this->hasOne(Horse::class);
    }

    public function profile(){
        return $this->belongsTo(Profile::class);
    }
    public function favoritedByUsers()
    {
        return $this->belongsToMany(User::class, 'favorite_auctions');
    }

}
