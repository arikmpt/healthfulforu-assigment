<?php

namespace Modules\Content\Actions\Interaction;

use Modules\Content\Models\Content;
use Modules\Content\Models\ContentInteraction;

class RecordShareAction
{
    public function execute(Content $content, int $userId, ?string $platform = null): void
    {
        $metadata = null;
        if ($platform) {
            $metadata = ['platform' => $platform];
        }

        // Record the share
        ContentInteraction::create([
            'user_id' => $userId,
            'content_id' => $content->id,
            'type' => 'share',
            'interacted_at' => now(),
            'metadata' => $metadata,
        ]);

        // Increment share count
        $content->increment('shares_count');
    }
}
