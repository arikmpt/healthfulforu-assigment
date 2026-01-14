<?php

namespace Modules\Content\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Content\Services\ContentService;
use Modules\Content\Services\ContentInteractionService;
use Modules\Content\Http\Requests\StoreContentRequest;
use Modules\Content\Http\Requests\UpdateContentRequest;
use Modules\Content\Http\Requests\FilterContentRequest;
use Modules\Content\Http\Resources\ContentResource;
use Modules\Content\Http\Resources\ContentDetailResource;
use Modules\Content\Models\Content;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    public function __construct(
        private readonly ContentService $contentService,
        private readonly ContentInteractionService $interactionService,
    ) {}

    /**
     * Get list content
     *
     * @group Content
     */
    public function index(FilterContentRequest $request): JsonResponse
    {
        $contents = $this->contentService->list(
            $request->validated(),
            $request->input('per_page', 15)
        );

        return $this->paginated(
            $contents->through(fn($content) => new ContentResource($content)),
            'Contents retrieved successfully'
        );
    }

    /**
     * Show content by slug
     *
     * @group Content
     */
    public function show(string $slug, Request $request): JsonResponse
    {
        $content = $this->contentService->getBySlug($slug);

        if (!$content) {
            return $this->notFound('Content not found');
        }

        if ($request->user()) {
            $this->interactionService->recordView($content, $request->user()->id);
        }

        return $this->success(
            new ContentDetailResource($content),
            'Content retrieved successfully'
        );
    }

    /**
     * Create a content
     *
     * @group Content
     */
    public function store(StoreContentRequest $request): JsonResponse
    {
        $content = $this->contentService->create($request->validated());

        return $this->created(
            new ContentDetailResource($content),
            'Content created successfully'
        );
    }

    /**
     * Update a content
     *
     * @group Content
     */
    public function update(UpdateContentRequest $request, Content $content): JsonResponse
    {
        $content = $this->contentService->update($content, $request->validated());

        return $this->success(
            new ContentDetailResource($content),
            'Content updated successfully'
        );
    }

    /**
     * Remove a content
     *
     * @group Content
     */
    public function destroy(Content $content): JsonResponse
    {
        $this->contentService->delete($content);

        return $this->noContent();
    }

    /**
     * Get recommended content
     *
     * @group Content
     */
    public function recommended(Request $request): JsonResponse
    {
        $contents = $this->contentService->getRecommended(
            $request->user()->id,
            $request->input('limit', 10)
        );

        return $this->success(
            ContentResource::collection($contents),
            'Recommended content retrieved successfully'
        );
    }
}
