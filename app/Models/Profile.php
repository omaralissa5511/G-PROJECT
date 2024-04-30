<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'profiles';

    protected $hidden = [
        'id',
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
}
