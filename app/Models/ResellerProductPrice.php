<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResellerProductPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'reseller_user_id',
        'product_id',
        'custom_price',
        'minimum_price',
    ];

    protected function casts(): array
    {
        return [
            'custom_price' => 'decimal:2',
            'minimum_price' => 'decimal:2',
        ];
    }

    public function reseller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reseller_user_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
