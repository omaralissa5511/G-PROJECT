<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class  Consultation extends Model
{
    use HasFactory;
    protected $fillable = [
        'content', 'sent_at', 'reply_content', 'reply_sent_at',
        'name',
        'age',
        'symptoms',
        'gender',
        'color',
        'profile_id',
        'health_care_id',
    ];

    protected $hidden=[
        'created_at',
        'updated_at'
    ];

    public function health_care(){
        return $this->belongsTo(HealthCare::class);
    }

    public function profile(){
        return $this->belongsTo(Profile::class);
    }
    public function consultation_details(){
        return $this->hasMany(ConsultationDetails::class);
    }

    public function consultation_images(){
        return $this->hasMany(ConsultationImage::class);
    }



}
