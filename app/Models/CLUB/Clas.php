<?php

namespace App\Models\CLUB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clas extends Model
{
    use HasFactory;
    protected $table = 'classes';
    protected $fillable = [
        'day','class','start',
        'end','course_id'
    ];

    public function courses (){
        return $this->belongsTo(Course::class);
    }
}
