<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'grandTotal',
        'totalItem',
        'totalPrice',
        'total_delivery_charge',
        'discount',
        'tax',
        'coupon',
        'payment_mode',
        'payment_status',
        'transaction_id',
        'address',
    ];

    public function order_item(): HasMany{
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function user():BelongsTo{
        return $this->belongsTo(User::class, 'user_id');
    }
}
