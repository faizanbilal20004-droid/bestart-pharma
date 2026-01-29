<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        
        'order_id',
        'item_id',
        'name',
        'price',
        'description',
        'quantity',
        'cover'
    ];

    public function order(): BelongsTo{
        return $this->belongsTo(Order::class , 'order_id');
    }

    public function item():BelongsTo{
        return $this->belongsTo(Gift::class, 'item_id');
    }
}
