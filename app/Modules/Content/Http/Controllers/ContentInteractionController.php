<?php

namespace Modules\Content\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Content\Services\ContentInteractionService;
use Modules\Content\Models\Content;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContentInteractionController extends Controller
{
    public function __construct(
        private readonly ContentInteractionService $service,
    ) {}

    /**
     * Toggle like on a content
     *
     * @group Content
     */
    public function toggleLike(Content $content, Request $request): JsonResponse
    {
        $result = $this->service->toggleLike($content, $request->user()->id);

        return $this->success($result, 'Like toggled successfully');
    }

    /**
     * Toggle bookmark on content
     * 
     * @group Content
     */
    public function toggleBookmark(Content $content, Request $request): JsonResponse
    {
        $result = $this->service->toggleBookmark($content, $request->user()->id);

        return $this->success($result, 'Bookmark toggled successfully');
    }

    /**
     * Record content share
     * 
     * @group Content
     */
    public function share(Content $content, Request $request): JsonResponse
    {
        $platform = $request->input('platform');

        $this->service->recordShare($content, $request->user()->id, $platform);

        return $this->success(null, 'Share recorded successfully');
    }
}
