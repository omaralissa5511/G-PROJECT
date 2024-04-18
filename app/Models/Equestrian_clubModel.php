<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equestrian_clubModel extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'equestrian_clubs';

    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
        'user_id'
    ];

    public function trainer (){
        return $this->hasMany(TrainerModel::class,'club_id');
    }
}
