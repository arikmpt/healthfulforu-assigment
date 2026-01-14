<?php

namespace Modules\Content\Actions\Interaction;

use Modules\Content\Models\Content;
use Modules\Content\Models\ContentInteraction;

class RecordViewAction
{
    public function execute(Content $content, int $userId): void
    {
        // Record the view
        ContentInteraction::create([
            'user_id' => $userId,
            'content_id' => $content->id,
            'type' => 'view',
            'interacted_at' => now(),
        ]);

        // Increment view count
        $content->incrementViewsCount();
    }
}
