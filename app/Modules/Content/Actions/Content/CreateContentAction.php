<?php

namespace Modules\Content\Actions\Content;

use Modules\Content\Models\Content;
use Illuminate\Support\Str;

class CreateContentAction
{
    public function execute(array $data): Content
    {
        // Extract topic IDs if provided
        $topicIds = $data['topic_ids'] ?? [];
        unset($data['topic_ids']);

        // Create content
        $content = Content::create($data);

        // Attach topics if provided
        if (!empty($topicIds)) {
            $syncData = [];
            foreach ($topicIds as $index => $topicId) {
                $syncData[$topicId] = ['is_primary' => $index === 0];
            }
            $content->topics()->sync($syncData);
        }

        return $content->load(['author', 'topics']);
    }
}
