<?php

namespace App\Models\CLUB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clas extends Model
{
    use HasFactory;
    protected $table = 'classes';
    protected $fillable = [
        'class','start','capacity','status',
        'counter','end','course_id','price'
    ];

    public function courses (){
        return $this->belongsTo(Course::class);
    }
}
