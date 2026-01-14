<?php

namespace Modules\Content\Actions\Interaction;

use Modules\Content\Models\Content;
use Modules\Content\Models\ContentInteraction;

class ToggleBookmarkAction
{
    public function execute(Content $content, int $userId): array
    {
        $interaction = ContentInteraction::where('user_id', $userId)
            ->where('content_id', $content->id)
            ->where('type', 'bookmark')
            ->first();

        if ($interaction) {
            // Remove bookmark
            $interaction->delete();
            $content->decrement('bookmarks_count');
            $isBookmarked = false;
        } else {
            // Add bookmark
            ContentInteraction::create([
                'user_id' => $userId,
                'content_id' => $content->id,
                'type' => 'bookmark',
                'interacted_at' => now(),
            ]);
            $content->increment('bookmarks_count');
            $isBookmarked = true;
        }

        return [
            'is_bookmarked' => $isBookmarked,
            'bookmarks_count' => $content->fresh()->bookmarks_count,
        ];
    }
}
