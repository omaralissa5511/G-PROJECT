<?php

namespace App\Models\CLUB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClubServise extends Model
{
    use HasFactory;

    protected $table = 'club_services';

    protected $fillable = [
        'club_id',
        'service_id',
    ];
}
