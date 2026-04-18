<?php

namespace App\Models;

use App\Enums\ServiceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'client_user_id',
        'reseller_user_id',
        'sub_reseller_user_id',
        'status',
        'metadata',
        'billing_started_on',
        'renews_on',
        'next_invoice_on',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ServiceStatus::class,
            'metadata' => 'array',
            'billing_started_on' => 'date',
            'renews_on' => 'date',
            'next_invoice_on' => 'date',
            'cancelled_at' => 'datetime',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_user_id');
    }

    public function reseller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reseller_user_id');
    }

    public function subReseller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sub_reseller_user_id');
    }

    public function domain(): HasOne
    {
        return $this->hasOne(Domain::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
