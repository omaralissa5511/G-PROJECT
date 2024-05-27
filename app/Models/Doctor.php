<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable=[
        'firstName',
        'lastName',
        'birth',
        'gender',
        'image',
        'description',
        'experience',
        'specialties',
        'user_id',
        'health_care_id',

    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];


    public function health_care(){
        return $this->belongsTo(HealthCare::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

}
