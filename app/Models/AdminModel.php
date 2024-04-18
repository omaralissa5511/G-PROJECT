<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
//use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class AdminModel extends Model
{
    use HasFactory , HasApiTokens , HasRoles;
    protected $guarded = [];
    protected $table = 'admins';
    protected $guard_name = 'spatie';

    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
        'user_id'
    ];
}
