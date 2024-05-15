<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageM extends Model
{
    use HasFactory;

    protected $table = 'messages';

    protected $fillable = [
        'user_id',
        'trainer_id',
        'user',
        'trainer',
        'content',
        'image',
        'role',
        'profile'
    ];
    protected $casts = [
        'image' => 'array',
    ];
}
