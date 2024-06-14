<?php

namespace App\Models\CLUB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferClub extends Model
{
    use HasFactory;
    protected $fillable=[
        'club_id',
        'offer_value',
        'description',
        'begin',
        'end',
    ];

    protected $hidden=[
        'created_at',
        'updated_at'
    ];

    public function club()
    {
        return $this->belongsTo(Equestrian_club::class);
    }


}
