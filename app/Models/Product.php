<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = ['category_id', 'name', 'description', 'price', 'stock', 'image_path'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems() 
    {
        return $this->hasMany(OrderItem::class);
    }

    protected function imagePath(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) =>
                $value
                    ? asset($value)
                    : null,
        );
    }
}
