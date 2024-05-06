<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auction extends Model
{
    use HasFactory;
    protected $table = 'auctions';
    protected $fillable = ['initialPrice','end','begin',
                   'description','profile_id','status'];
    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
    ];
    public function bids(){
        return $this->hasMany(Bid::class);
    }
    public function horses(){
        return $this->hasOne(Horse::class);
    }
}
