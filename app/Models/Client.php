<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'telegram_chat_id',
        'telegram_username',
        'preferred_channel',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(AccountClient::class);
    }

    public function activeSubscriptions(): HasMany
    {
        return $this->hasMany(AccountClient::class)->whereNull('ended_at')->where('status', 'active');
    }

    public function accounts(): BelongsToMany
    {
        return $this->belongsToMany(Account::class, 'account_client')
            ->withPivot(['slot_label', 'client_price', 'currency', 'payment_day', 'started_at', 'ended_at', 'status'])
            ->withTimestamps();
    }

    public function notificationLogs(): HasMany
    {
        return $this->hasMany(NotificationLog::class);
    }
}
