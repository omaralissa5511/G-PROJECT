<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerBuyerModel extends Model
{
    use HasFactory;
    protected $table = 'sellerbuyers';
    protected $guarded = [];

    protected $hidden = [
    'id',
    'created_at',
    'updated_at',
    'user_id'
];
}
