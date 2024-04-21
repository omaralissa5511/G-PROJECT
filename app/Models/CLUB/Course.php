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
        'description','price','duration','begin',
        'end','valid','trainer_id','service_id'
    ];
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
