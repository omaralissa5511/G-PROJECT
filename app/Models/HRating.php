<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HRating extends Model
{
    use HasFactory;
    protected $fillable = [
        'health_care_id',
        'profile_id',
        'rating',
        'review',
    ];

    public function health_care()
    {
        return $this->belongsTo(HealthCare::class);
    }
    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
    public function HRatings()
    {
        return $this->hasMany(HRating::class);
    }
}
