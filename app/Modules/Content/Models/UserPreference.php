<?php

namespace Modules\Content\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Auth\Models\User;

class UserPreference extends Model
{
    protected $fillable = [
        'user_id',
        'topic_id',
        'interest_level',
    ];

    protected $casts = [
        'interest_level' => 'integer',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }
}
