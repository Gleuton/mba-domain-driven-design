<?php

namespace App\Common\Application;

use App\Common\Domain\AggregateRoot;
use Dotenv\Repository\RepositoryInterface;

interface UnitOfWorkInterface
{

    public function register(AggregateRoot $aggregate): void;

    public function registerRemoved(AggregateRoot $aggregate): void;

    public function commit(): void;

    public function rollback(): void;

    public function hasPendingChanges(): bool;

    public function flush(): void;

    public function registerRepository(string $entityClass, $repository): void;
}