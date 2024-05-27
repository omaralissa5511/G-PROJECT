<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsultationDetails extends Model
{
    use HasFactory;

    protected $fillable=[
        'consultation_id',
        'details',
        'date',
        'type'
    ];
}
