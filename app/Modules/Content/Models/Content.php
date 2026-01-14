<?php

namespace Modules\Content\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Modules\Auth\Models\User;

class Content extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'title',
        'slug',
        'summary',
        'body',
        'video_url',
        'thumbnail_url',
        'type',
        'access_level',
        'status',
        'author_id',
        'published_at',
        'duration_minutes',
        'read_time_minutes',
        'metadata',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'metadata' => 'array',
        'views_count' => 'integer',
        'likes_count' => 'integer',
        'shares_count' => 'integer',
        'bookmarks_count' => 'integer',
        'duration_minutes' => 'integer',
        'read_time_minutes' => 'integer',
    ];

    /**
     * Override default laravel factory namespace
     *
     */
    protected static function newFactory()
    {
        return \Modules\Content\Database\Factories\ContentFactory::new();
    }

    protected static function booted(): void
    {
        static::creating(function (Content $content) {
            $content->uuid = (string) Str::uuid();
            if (empty($content->slug)) {
                $content->slug = Str::slug($content->title);
            }
        });
    }

    // Relationships
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function topics()
    {
        return $this->belongsToMany(Topic::class, 'content_topics')
            ->withPivot('is_primary')
            ->withTimestamps();
    }

    public function primaryTopic()
    {
        return $this->belongsToMany(Topic::class, 'content_topics')
            ->wherePivot('is_primary', true)
            ->withTimestamps();
    }

    public function interactions()
    {
        return $this->hasMany(ContentInteraction::class);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeFree($query)
    {
        return $query->where('access_level', 'free');
    }

    public function scopePremium($query)
    {
        return $query->where('access_level', 'premium');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    // Helper methods
    public function isPremium(): bool
    {
        return $this->access_level === 'premium';
    }

    public function isPublished(): bool
    {
        return $this->status === 'published'
            && $this->published_at !== null
            && $this->published_at <= now();
    }

    public function incrementViewsCount(): void
    {
        $this->increment('views_count');
    }
}
