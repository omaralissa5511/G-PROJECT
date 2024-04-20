<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HealthCareModel extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'health_cares';

    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
        'user_id'
    ];
}
