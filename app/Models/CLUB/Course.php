<?php

namespace App\Models\CLUB;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;
    protected $table = 'courses';
    protected $fillable = [
        'description','price','duration','begin','club',
        'end','valid','trainer_id','service_id'
    ];
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
    public function resrvations()
    {
        return $this->hasMany(Reservation::class);
    }
    public function clases()
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
