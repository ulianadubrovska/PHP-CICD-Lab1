<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'sku',
        'description',
        'price',
        'stock_quantity',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock_quantity' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    // Товар належить одній категорії
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Товар може входити до багатьох позицій замовлень
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // Товар може входити до багатьох замовлень
    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(
            Order::class,
            'order_items'
        )->withPivot([
            'quantity',
            'unit_price',
        ])->withTimestamps();
    }

    // Товар може знаходитися в кошиках різних покупців
    public function carts(): BelongsToMany
    {
        return $this->belongsToMany(
            Cart::class,
            'cart_items'
        )->withPivot('quantity')->withTimestamps();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Товар може мати багато відгуків
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
