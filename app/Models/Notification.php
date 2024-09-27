<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Notification extends Model
{
    use Notifiable;

    protected $fillable = [
        'type', 'notifiable_id', 'notifiable_type', 'title', 'message', 'data', 'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    /**
     * Get the owning notifiable model.
     */
    public function notifiable()
    {
        return $this->morphTo();
    }

    public function user()
    {
    $this->belongsTo(User::class) ;
    }
}
