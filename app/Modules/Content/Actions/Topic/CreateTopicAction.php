<?php

namespace Modules\Content\Actions\Topic;

use Modules\Content\Models\Topic;

class CreateTopicAction
{
    public function execute(array $data): Topic
    {
        return Topic::create($data);
    }
}
