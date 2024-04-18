<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainerModel extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'trainers';

    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
        'user_id'
    ];
}
