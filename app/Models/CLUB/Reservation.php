<?php

namespace App\Models\CLUB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $table = 'reservations';

    protected $fillable = [
        'user_id',
        'course_id',
        'clas',
        'number_of_people',
        'status',
        'price'
    ];

    public function course()
    {
        return $this->belongsTo('App\Models\CLUB\Course');
    }
}
