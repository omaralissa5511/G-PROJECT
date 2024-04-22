<?php

namespace App\Models\CLUB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClubImage extends Model
{
    use HasFactory;

    protected $table = 'club_images';

    protected $fillable = [
        'club_id',
        'image_paths',
    ];

    protected $casts = [
        'image_paths' => 'array',
    ];

}
