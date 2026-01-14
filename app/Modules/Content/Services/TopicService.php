<?php

namespace Modules\Content\Services;

use Modules\Content\Actions\Topic\CreateTopicAction;
use Modules\Content\Actions\Topic\UpdateTopicAction;
use Modules\Content\Actions\Topic\DeleteTopicAction;
use Modules\Content\Repositories\TopicRepository;
use Modules\Content\Models\Topic;
use Illuminate\Support\Collection;

class TopicService
{
    public function __construct(
        private readonly CreateTopicAction $createAction,
        private readonly UpdateTopicAction $updateAction,
        private readonly DeleteTopicAction $deleteAction,
        private readonly TopicRepository $repository,
    ) {}

    public function getAll(): Collection
    {
        return $this->repository->getActiveTopics();
    }

    public function getByType(string $type): Collection
    {
        return $this->repository->getTopicsByType($type);
    }

    public function getRootTopics(): Collection
    {
        return $this->repository->getRootTopics();
    }

    public function create(array $data): Topic
    {
        $topic = $this->createAction->execute($data);
        $this->repository->clearTopicCache();
        return $topic;
    }

    public function update(Topic $topic, array $data): Topic
    {
        $topic = $this->updateAction->execute($topic, $data);
        $this->repository->clearTopicCache();
        return $topic;
    }

    public function delete(Topic $topic): bool
    {
        $result = $this->deleteAction->execute($topic);
        $this->repository->clearTopicCache();
        return $result;
    }
}
