<?php

namespace Modules\Content\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Auth\Models\User;

class ContentInteraction extends Model
{
    protected $fillable = [
        'user_id',
        'content_id',
        'type',
        'interacted_at',
        'metadata',
    ];

    protected $casts = [
        'interacted_at' => 'datetime',
        'metadata' => 'array',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function content()
    {
        return $this->belongsTo(Content::class);
    }

    // Scopes
    public function scopeViews($query)
    {
        return $query->where('type', 'view');
    }

    public function scopeLikes($query)
    {
        return $query->where('type', 'like');
    }

    public function scopeBookmarks($query)
    {
        return $query->where('type', 'bookmark');
    }

    public function scopeShares($query)
    {
        return $query->where('type', 'share');
    }
}
