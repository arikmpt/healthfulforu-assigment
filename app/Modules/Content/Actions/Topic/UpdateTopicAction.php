<?php

namespace Modules\Content\Actions\Topic;

use Modules\Content\Models\Topic;

class UpdateTopicAction
{
    public function execute(Topic $topic, array $data): Topic
    {
        $topic->update($data);
        return $topic->fresh();
    }
}
