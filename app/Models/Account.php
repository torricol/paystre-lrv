<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'streaming_service_id',
        'label',
        'email',
        'password',
        'extra_credentials',
        'plan_name',
        'cost',
        'currency',
        'billing_day',
        'next_billing_date',
        'max_slots',
        'status',
        'notes',
    ];

    protected $casts = [
        'password' => 'encrypted',
        'extra_credentials' => 'encrypted',
        'cost' => 'decimal:2',
        'billing_day' => 'integer',
        'next_billing_date' => 'date',
        'max_slots' => 'integer',
    ];

    public function streamingService(): BelongsTo
    {
        return $this->belongsTo(StreamingService::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(AccountClient::class);
    }

    public function activeSubscriptions(): HasMany
    {
        return $this->hasMany(AccountClient::class)->whereNull('ended_at')->where('status', 'active');
    }

    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(Client::class, 'account_client')
            ->withPivot(['slot_label', 'pin', 'client_price', 'currency', 'payment_day', 'started_at', 'ended_at', 'status'])
            ->withTimestamps();
    }

    public function availableSlots(): int
    {
        return $this->max_slots - $this->activeSubscriptions()->count();
    }
}
