<?php

namespace App\Models\CLUB;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $table = 'courses';
    protected $fillable = [
        'description','duration','begin','club',
        'days','end','valid','trainer_id','service_id'
    ];
    protected static function booted()
    {
        static::retrieved(function ($course) {
            $endDate = Carbon::parse($course->end);
            if ($endDate->isPast()) {
                $course->update(['valid' => false]);
            }
       });
    }
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
    public function resrvations()
    {
        return $this->hasMany(Reservation::class);
    }
    public function classes()
    {
        return $this->hasMany(Clas::class);
    }


    public function service()
    {
        return $this->belongsTo('App\Models\CLUB\Service');
    }

    public function trainer()
    {
        return $this->belongsTo('App\Models\CLUB\Trainer');
    }

}
