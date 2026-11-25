<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ordered_at',
        'status',
        'total_amount',
        'comment',
    ];

    protected function casts(): array
    {
        return [
            'ordered_at' => 'datetime',
            'total_amount' => 'decimal:2',
        ];
    }

    // Замовлення належить одному покупцю
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Замовлення містить багато позицій
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // Замовлення може містити багато товарів
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'order_items'
        )->withPivot([
            'quantity',
            'unit_price',
        ])->withTimestamps();
    }

    // Замовлення має один запис оплати
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    // Замовлення має один запис доставки
    public function delivery(): HasOne
    {
        return $this->hasOne(Delivery::class);
    }
}
