<?php

namespace Modules\Content\Actions\Topic;

use Modules\Content\Models\Topic;

class DeleteTopicAction
{
    public function execute(Topic $topic): bool
    {
        return $topic->delete();
    }
}
