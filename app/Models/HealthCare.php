<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HealthCare extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'health_cares';

    protected $fillable =[
        'name',
        'address',
        'description',
        'profile_image',
        'license',
        'website',
        'lat',
        'long',
        'day',
        'start',
        'end',
        'user_id'
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function doctors()
    {
        return $this->hasMany(Doctor::class);
    }

    public function HRatings()
    {
        return $this->hasMany(HRating::class);
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }


}
