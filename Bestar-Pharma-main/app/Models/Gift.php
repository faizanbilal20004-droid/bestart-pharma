<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gift extends Model
{
    protected $fillable = [
        'name',
        'price',
        'deal_price',
        'stock',
        'cover',
        'status',
        'description',
        'rating',
        'prescription',
        'mfg',
        'packSize',
        'type'

    ];
}
