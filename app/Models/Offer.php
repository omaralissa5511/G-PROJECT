<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

    protected $fillable=[
      'health_care_id',
      'offer_value',
      'description',
      'begin',
      'end',
    ];

    protected $hidden=[
        'created_at',
        'updated_at'
    ];

    public function health_care()
    {
        return $this->belongsTo(HealthCare::class);
    }
}
