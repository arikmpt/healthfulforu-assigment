<?php

namespace Modules\Content\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Content\Services\TopicService;
use Modules\Content\Http\Requests\StoreTopicRequest;
use Modules\Content\Http\Requests\UpdateTopicRequest;
use Modules\Content\Http\Resources\TopicResource;
use Modules\Content\Models\Topic;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TopicController extends Controller
{
    public function __construct(
        private readonly TopicService $topicService,
    ) {}

    /**
     * List all active topics
     * 
     * @group Content
     */
    public function index(Request $request): JsonResponse
    {
        $type = $request->input('type');

        $topics = $type
            ? $this->topicService->getByType($type)
            : $this->topicService->getAll();

        return $this->success(
            TopicResource::collection($topics),
            'Topics retrieved successfully'
        );
    }

    /**
     * Get topic details
     * 
     * @group Content
     */
    public function show(Topic $topic): JsonResponse
    {
        return $this->success(
            new TopicResource($topic->load('children')),
            'Topic retrieved successfully'
        );
    }

    /**
     * Create new topic (admin only)
     * 
     * @group Content
     */
    public function store(StoreTopicRequest $request): JsonResponse
    {
        $topic = $this->topicService->create($request->validated());

        return $this->created(
            new TopicResource($topic),
            'Topic created successfully'
        );
    }

    /**
     * Update topic (admin only)
     * 
     * @group Content
     */
    public function update(UpdateTopicRequest $request, Topic $topic): JsonResponse
    {
        $topic = $this->topicService->update($topic, $request->validated());

        return $this->success(
            new TopicResource($topic),
            'Topic updated successfully'
        );
    }

    /**
     * Delete topic (admin only)
     * 
     * @group Content
     */
    public function destroy(Topic $topic): JsonResponse
    {
        $this->topicService->delete($topic);

        return $this->noContent();
    }
}
