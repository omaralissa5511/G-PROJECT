<?php

namespace App\Models\CLUB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $table = 'services';

    protected $fillable = [
        'name',
        'image',
        'description',
        'category_id',
        'club_id'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function club()
    {
        return $this->belongsTo(Equestrian_club::class);
    }
    public function trainers()
    {
        return $this->belongsToMany(Trainer::class,'courses');
    }
// للحجز الفردي
    public function b_trainers()
    {
        return $this->belongsToMany(Trainer::class, 'trainer_service');
    }
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
