<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class StreamingService extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'color',
        'max_slots',
        'website_url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'max_slots' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (StreamingService $service) {
            if (empty($service->slug)) {
                $service->slug = Str::slug($service->name);
            }
        });
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }

    public function activeAccounts(): HasMany
    {
        return $this->hasMany(Account::class)->where('status', 'active');
    }
}
