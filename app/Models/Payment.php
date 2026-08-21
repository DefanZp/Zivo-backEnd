<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'gateway',
        'gateway_order_id',
        'gateway_transaction_id',
        'payment_method',
        'payment_status',
        'amount',
        'paid_at',
    ];

    public function order() 
    {
        return $this->belongsTo(Order::class);
    }
}
