<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insurance extends Model
{
    use HasFactory;

    protected $table = 'insurances';
    protected  $fillable = ['insurance','auction','profile_id'];
    protected $hidden = [];

    public function profile(){
        return $this->belongsTo(Profile::class);
    }
}
