<?php

namespace App\Models;

use App\Enums\ServiceRequestStatus;
use App\Models\Scopes\VisibleServiceRequestsScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceRequest extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::addGlobalScope(new VisibleServiceRequestsScope);
    }

    protected $fillable = [
        'client_user_id',
        'reseller_user_id',
        'title',
        'description',
        'status',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'status' => ServiceRequestStatus::class,
            'metadata' => 'array',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_user_id');
    }

    public function reseller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reseller_user_id');
    }
}
