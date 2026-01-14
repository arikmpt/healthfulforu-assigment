<?php

namespace Modules\Content\Actions\Interaction;

use Modules\Content\Models\Content;
use Modules\Content\Models\ContentInteraction;
use Illuminate\Support\Facades\DB;

class ToggleLikeAction
{
    public function execute(Content $content, int $userId): array
    {
        $interaction = ContentInteraction::where('user_id', $userId)
            ->where('content_id', $content->id)
            ->where('type', 'like')
            ->first();

        if ($interaction) {
            // Unlike: delete the interaction
            $interaction->delete();
            $content->decrement('likes_count');
            $isLiked = false;
        } else {
            // Like: create the interaction
            ContentInteraction::create([
                'user_id' => $userId,
                'content_id' => $content->id,
                'type' => 'like',
                'interacted_at' => now(),
            ]);
            $content->increment('likes_count');
            $isLiked = true;
        }

        return [
            'is_liked' => $isLiked,
            'likes_count' => $content->fresh()->likes_count,
        ];
    }
}
