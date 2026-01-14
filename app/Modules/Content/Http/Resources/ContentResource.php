<?php

namespace Modules\Content\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'title' => $this->title,
            'slug' => $this->slug,
            'summary' => $this->summary,
            'thumbnail_url' => $this->thumbnail_url,
            'type' => $this->type,
            'access_level' => $this->access_level,
            'status' => $this->status,
            'published_at' => $this->published_at?->toISOString(),
            'duration_minutes' => $this->duration_minutes,
            'read_time_minutes' => $this->read_time_minutes,
            'views_count' => $this->views_count,
            'likes_count' => $this->likes_count,
            'shares_count' => $this->shares_count,
            'bookmarks_count' => $this->bookmarks_count,
            'author' => [
                'id' => $this->author->id,
                'name' => $this->author->profile->first_name . ' ' . $this->author->profile->last_name,
                'avatar_url' => $this->author->profile->avatar_url,
            ],
            'topics' => TopicResource::collection($this->whenLoaded('topics')),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
