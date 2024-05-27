<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bid extends Model
{
    use HasFactory;

    protected $table = 'bids';
    protected $fillable = ['offeredPrice','profile_id','auction_id'];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function auctions(){
        return $this->belongsTo(Auction::class);
    }
    public function profile(){
        return $this->belongsTo(Profile::class);
    }
}
