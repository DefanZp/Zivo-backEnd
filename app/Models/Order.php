<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',

        // Address snapshot
        'recipient_name',
        'phone',
        'full_address',

        'province_id',
        'province_name',

        'city_id',
        'city_name',

        'district_id',
        'district_name',

        'subdistrict_id',
        'subdistrict_name',

        'postal_code',

        'latitude',
        'longitude',

        // Order
        'total_price',
        'status',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function items() {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
