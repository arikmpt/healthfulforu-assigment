<?php

namespace Modules\Content\Repositories;

use App\Support\Repositories\CacheableRepository;
use Modules\Content\Models\Content;
use Modules\Content\Models\UserPreference;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ContentRepository extends CacheableRepository
{
    protected function cacheTags(): array
    {
        return ['content'];
    }

    public function getPublishedContent(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $cacheKey = 'content:published:' . md5(json_encode($filters)) . ':' . $perPage;

        return $this->remember($cacheKey, $this->ttlShort, function () use ($filters, $perPage) {
            $query = Content::query()
                ->with(['author', 'topics', 'primaryTopic'])
                ->published();

            // Apply filters
            if (!empty($filters['type'])) {
                $query->byType($filters['type']);
            }

            if (!empty($filters['access_level'])) {
                $query->where('access_level', $filters['access_level']);
            }

            if (!empty($filters['topic_id'])) {
                $query->whereHas('topics', function ($q) use ($filters) {
                    $q->where('topics.id', $filters['topic_id']);
                });
            }

            if (!empty($filters['search'])) {
                $query->where(function ($q) use ($filters) {
                    $q->where('title', 'ILIKE', "%{$filters['search']}%")
                        ->orWhere('summary', 'ILIKE', "%{$filters['search']}%");
                });
            }

            // Sorting
            $sortBy = $filters['sort_by'] ?? 'published_at';
            $sortOrder = $filters['sort_order'] ?? 'desc';
            $query->orderBy($sortBy, $sortOrder);

            return $query->paginate($perPage);
        });
    }

    public function findBySlug(string $slug): ?Content
    {
        $cacheKey = "content:slug:{$slug}";

        return $this->remember($cacheKey, $this->ttlMedium, function () use ($slug) {
            return Content::with(['author', 'topics'])
                ->where('slug', $slug)
                ->published()
                ->first();
        });
    }

    public function getRecommendedForUser(int $userId, int $limit = 10): array
    {
        $cacheKey = "content:recommended:{$userId}:{$limit}";

        return $this->remember($cacheKey, $this->ttlShort, function () use ($userId, $limit) {
            // Get user's preferred topics
            $preferredTopicIds = UserPreference::where('user_id', $userId)
                ->orderBy('interest_level', 'desc')
                ->pluck('topic_id')
                ->toArray();

            if (empty($preferredTopicIds)) {
                // Return popular content if no preferences
                return Content::published()
                    ->orderBy('views_count', 'desc')
                    ->limit($limit)
                    ->get()
                    ->toArray();
            }

            return Content::published()
                ->whereHas('topics', function ($q) use ($preferredTopicIds) {
                    $q->whereIn('topics.id', $preferredTopicIds);
                })
                ->orderBy('published_at', 'desc')
                ->limit($limit)
                ->get()
                ->toArray();
        });
    }

    public function clearContentCache(): void
    {
        $this->flushCache();
    }
}
