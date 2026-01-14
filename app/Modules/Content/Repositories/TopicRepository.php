<?php

namespace Modules\Content\Repositories;

use App\Support\Repositories\CacheableRepository;
use Modules\Content\Models\Topic;
use Illuminate\Support\Collection;

class TopicRepository extends CacheableRepository
{
    protected function cacheTags(): array
    {
        return ['topics'];
    }

    public function getActiveTopics(): Collection
    {
        $cacheKey = 'topics:active:all';

        return $this->remember($cacheKey, $this->ttlLong, function () {
            return Topic::active()
                ->with('children')
                ->orderBy('sort_order')
                ->get();
        });
    }

    public function getTopicsByType(string $type): Collection
    {
        $cacheKey = "topics:type:{$type}";

        return $this->remember($cacheKey, $this->ttlLong, function () use ($type) {
            return Topic::active()
                ->byType($type)
                ->orderBy('sort_order')
                ->get();
        });
    }

    public function getRootTopics(): Collection
    {
        $cacheKey = 'topics:root';

        return $this->remember($cacheKey, $this->ttlLong, function () {
            return Topic::active()
                ->rootLevel()
                ->with('children')
                ->orderBy('sort_order')
                ->get();
        });
    }

    public function clearTopicCache(): void
    {
        $this->flushCache();
    }
}
