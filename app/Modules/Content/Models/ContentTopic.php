<?php

namespace Modules\Content\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ContentTopic extends Pivot
{
    protected $table = 'content_topics';

    protected $fillable = [
        'content_id',
        'topic_id',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public $incrementing = true;

    // Relationships
    public function content()
    {
        return $this->belongsTo(Content::class);
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }
}
