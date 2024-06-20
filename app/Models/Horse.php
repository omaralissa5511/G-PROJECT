<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Horse extends Model
{
    use HasFactory;
    protected $table = 'horses';
    protected $fillable = ['name','color','category','birth',
                           'gender','address','auction_id','images','video'];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    protected $casts = ['images'=>'array'];

}
