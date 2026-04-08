<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccountClient extends Model
{
    protected $table = 'account_client';

    protected $fillable = [
        'account_id',
        'client_id',
        'slot_label',
        'pin',
        'client_price',
        'currency',
        'payment_day',
        'started_at',
        'ended_at',
        'status',
    ];

    protected $casts = [
        'pin' => 'encrypted',
        'client_price' => 'decimal:2',
        'payment_day' => 'integer',
        'started_at' => 'date',
        'ended_at' => 'date',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function notificationLogs(): HasMany
    {
        return $this->hasMany(NotificationLog::class);
    }

    public function scopeActive($query)
    {
        return $query->whereNull('ended_at')->where('status', 'active');
    }
}
