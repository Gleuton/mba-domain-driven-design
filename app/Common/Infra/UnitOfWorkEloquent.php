<?php

namespace App\Common\Infra;

use App\Common\Application\UnitOfWorkInterface;
use App\Common\Domain\AggregateRoot;
use Exception;
use Illuminate\Database\DatabaseManager;
use RuntimeException;
use Throwable;

class UnitOfWorkEloquent implements UnitOfWorkInterface
{
    protected array $newEntities = [];
    protected array $removedEntities = [];
    protected array $repositories = [];
    protected DatabaseManager $database;

    public function __construct(DatabaseManager $database)
    {
        $this->database = $database;
    }

    public function register(AggregateRoot $aggregate): void
    {
        $this->newEntities[] = $aggregate;
    }

    public function registerRemoved(AggregateRoot $aggregate): void
    {
        $this->removedEntities[] = $aggregate;
    }

    /**
     * @throws Throwable
     */
    public function commit(): void
    {
        try {
            $this->database->beginTransaction();

            $this->persistNewEntities();
            $this->removeEntities();

            $this->database->commit();
        } catch (Throwable $e) {
            $this->database->rollBack();
            throw $e;
        } finally {
            $this->clear();
        }
    }

    /**
     * @throws Throwable
     */
    public function rollback(): void
    {
        $this->database->rollBack();
        $this->clear();
    }

    protected function persistNewEntities(): void
    {
        foreach ($this->newEntities as $entity) {
            $repository = $this->getRepositoryFor($entity);
            $repository->save($entity);
        }
    }

    protected function removeEntities(): void
    {
        foreach ($this->removedEntities as $entity) {
            $repository = $this->getRepositoryFor($entity);
            $repository->remove($entity);
        }
    }


    protected function getRepositoryFor(AggregateRoot $aggregate)
    {
        $entityClass = get_class($aggregate);

        if (!isset($this->repositories[$entityClass])) {
            throw new RuntimeException("Repository not registered for entity: $entityClass");
        }

        return $this->repositories[$entityClass];
    }

    public function registerRepository(string $entityClass, $repository): void
    {
        $this->repositories[$entityClass] = $repository;
    }

    public function hasPendingChanges(): bool
    {
        return !empty($this->newEntities) || !empty($this->removedEntities);
    }

    public function flush(): void
    {
        if ($this->hasPendingChanges()) {
            $this->commit();
        }
    }

    protected function clear(): void
    {
        $this->newEntities = [];
        $this->removedEntities = [];
    }
}