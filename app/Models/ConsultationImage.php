<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsultationImage extends Model
{
    use HasFactory;
    protected $fillable = [
        'consultation_id',
        'image',
    ];

    protected $hidden=[
        'created_at',
        'updated_at'
    ];

  protected $casts = [
        'image' => 'array',
    ];
    public function consultation(){
        return $this->belongsTo(Consultation::class);
    }
}
