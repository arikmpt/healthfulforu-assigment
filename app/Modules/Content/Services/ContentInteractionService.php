<?php

namespace Modules\Content\Services;

use Modules\Content\Actions\Interaction\RecordViewAction;
use Modules\Content\Actions\Interaction\ToggleLikeAction;
use Modules\Content\Actions\Interaction\ToggleBookmarkAction;
use Modules\Content\Actions\Interaction\RecordShareAction;
use Modules\Content\Models\Content;

class ContentInteractionService
{
    public function __construct(
        private readonly RecordViewAction $recordViewAction,
        private readonly ToggleLikeAction $toggleLikeAction,
        private readonly ToggleBookmarkAction $toggleBookmarkAction,
        private readonly RecordShareAction $recordShareAction,
    ) {}

    public function recordView(Content $content, int $userId): void
    {
        $this->recordViewAction->execute($content, $userId);
    }

    public function toggleLike(Content $content, int $userId): array
    {
        return $this->toggleLikeAction->execute($content, $userId);
    }

    public function toggleBookmark(Content $content, int $userId): array
    {
        return $this->toggleBookmarkAction->execute($content, $userId);
    }

    public function recordShare(Content $content, int $userId, ?string $platform = null): void
    {
        $this->recordShareAction->execute($content, $userId, $platform);
    }
}
