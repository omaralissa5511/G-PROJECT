<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageD extends Model
{
    use HasFactory;

    protected $table = 'd_messages';

    protected $fillable = [
        'user_id',
        'doctor_id',
        'user',
        'doctor',
        'content',
        'image',
        'role',
        'time',
        'read'
    ];
    protected $casts = [
        'image' => 'array',
    ];
}
