<?php

namespace App\Models\CLUB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trainer extends Model
{
    use HasFactory;
    //protected $guarded = [];
    protected $fillable = ['FName','channelName','lName','birth','gender',
        'address','license','image','qualifications','certifications',
        'experience','specialties','user_id','club_id'];
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
}
