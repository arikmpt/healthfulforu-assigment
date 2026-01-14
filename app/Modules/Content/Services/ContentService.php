<?php

namespace Modules\Content\Services;

use Modules\Content\Actions\Content\CreateContentAction;
use Modules\Content\Actions\Content\UpdateContentAction;
use Modules\Content\Actions\Content\DeleteContentAction;
use Modules\Content\Repositories\ContentRepository;
use Modules\Content\Models\Content;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ContentService
{
    public function __construct(
        private readonly CreateContentAction $createAction,
        private readonly UpdateContentAction $updateAction,
        private readonly DeleteContentAction $deleteAction,
        private readonly ContentRepository $repository,
    ) {}

    public function list(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getPublishedContent($filters, $perPage);
    }

    public function getBySlug(string $slug): ?Content
    {
        return $this->repository->findBySlug($slug);
    }

    public function create(array $data): Content
    {
        $content = $this->createAction->execute($data);
        $this->repository->clearContentCache();
        return $content;
    }

    public function update(Content $content, array $data): Content
    {
        $content = $this->updateAction->execute($content, $data);
        $this->repository->clearContentCache();
        return $content;
    }

    public function delete(Content $content): bool
    {
        $result = $this->deleteAction->execute($content);
        $this->repository->clearContentCache();
        return $result;
    }

    public function getRecommended(int $userId, int $limit = 10): array
    {
        return $this->repository->getRecommendedForUser($userId, $limit);
    }
}
