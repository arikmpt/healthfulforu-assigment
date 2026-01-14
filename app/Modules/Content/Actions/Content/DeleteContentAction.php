<?php

namespace Modules\Content\Actions\Content;

use Modules\Content\Models\Content;

class DeleteContentAction
{
    public function execute(Content $content): bool
    {
        return $content->delete();
    }
}
