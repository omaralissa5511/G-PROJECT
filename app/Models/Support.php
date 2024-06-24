<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Support extends Model
{
    use HasFactory;

    protected $fillable=['email','phone_number','message','reply'];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
