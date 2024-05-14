<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppMessage extends Model
{
    use HasFactory;
    protected $fillable = ['message'];

}
