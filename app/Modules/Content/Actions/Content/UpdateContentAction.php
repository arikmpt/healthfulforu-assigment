<?php

namespace Modules\Content\Actions\Content;

use Modules\Content\Models\Content;

class UpdateContentAction
{
    public function execute(Content $content, array $data): Content
    {
        // Extract topic IDs if provided
        $topicIds = $data['topic_ids'] ?? null;
        unset($data['topic_ids']);

        // Update content
        $content->update($data);

        // Update topics if provided
        if ($topicIds !== null) {
            $syncData = [];
            foreach ($topicIds as $index => $topicId) {
                $syncData[$topicId] = ['is_primary' => $index === 0];
            }
            $content->topics()->sync($syncData);
        }

        return $content->fresh(['author', 'topics']);
    }
}
