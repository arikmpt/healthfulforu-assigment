<?php

namespace Modules\Content\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Content\Http\Requests\StoreUserPreferenceRequest;
use Modules\Content\Http\Resources\UserPreferenceResource;
use Modules\Content\Models\UserPreference;
use Modules\Content\Models\Topic;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserPreferenceController extends Controller
{
    /**
     * Get user's topic preferences
     * 
     * @group Content
     */
    public function index(Request $request): JsonResponse
    {
        $preferences = UserPreference::where('user_id', $request->user()->id)
            ->with('topic')
            ->orderBy('interest_level', 'desc')
            ->get();

        return $this->success(
            UserPreferenceResource::collection($preferences),
            'Preferences retrieved successfully'
        );
    }

    /**
     * Add or update topic preference
     * 
     * @group Content
     */
    public function store(StoreUserPreferenceRequest $request): JsonResponse
    {
        $preference = UserPreference::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'topic_id' => $request->input('topic_id'),
            ],
            [
                'interest_level' => $request->input('interest_level', 5),
            ]
        );

        $preference->load('topic');

        return $this->success(
            new UserPreferenceResource($preference),
            'Preference saved successfully'
        );
    }

    /**
     * Remove topic preference
     * 
     * @group Content
     */
    public function destroy(Topic $topic, Request $request): JsonResponse
    {
        UserPreference::where('user_id', $request->user()->id)
            ->where('topic_id', $topic->id)
            ->delete();

        return $this->noContent();
    }
}
