<?php

namespace App\Models;

use App\Enums\ProductType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'description',
        'product_type',
        'base_price',
        'sale_price',
        'config',
    ];

    protected function casts(): array
    {
        return [
            'product_type' => ProductType::class,
            'base_price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'config' => 'array',
        ];
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function resellerPrices(): HasMany
    {
        return $this->hasMany(ResellerProductPrice::class);
    }
}
