<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageTemplate extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'category',
        'body',
        'channel',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeForChannel($query, string $channel)
    {
        return $query->where(function ($q) use ($channel) {
            $q->where('channel', $channel)->orWhere('channel', 'any');
        });
    }
}
