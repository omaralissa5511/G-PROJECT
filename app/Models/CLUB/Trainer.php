<?php

namespace App\Models\CLUB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trainer extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'trainers';

    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
    ];

    public function services()
    {
        return $this->belongsToMany(Service::class,'courses');
    }
    public function services_trianers()
    {
        return $this->belongsToMany(Service::class,'service');
    }
    public function clubs (){
        return $this->belongsTo(Equestrian_club::class);
    }
    public function TRatings()
    {
        return $this->hasMany(TRating::class);
    }

    public function Courses()
    {
        return $this->hasMany(Course::class);
    }

    public function trainertimes()
    {
        return $this->hasMany(TrainerTime::class);
    }
// للحجز الفردي
    public function b_services()
    {
        return $this->belongsToMany(Service::class, 'trainer_service');
    }
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
