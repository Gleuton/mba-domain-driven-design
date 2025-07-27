<?php

namespace App\Common\Domain;

use SplObjectStorage;
use Traversable;

abstract class AbstractCollection implements \Countable, \IteratorAggregate
{
    private SplObjectStorage $storage;

    public function __construct()
    {
        $this->storage = new SplObjectStorage();
    }

    public function add(AbstractEntity $entity): self
    {
        $this->validate($entity);

        if (!$this->contains($entity)) {
            $this->storage->attach($entity);
        }
        return $this;
    }

    public function remove(AbstractEntity $entity): self
    {
        $this->storage->detach($entity);
        return $this;
    }

    public function find(callable $callback): ?AbstractEntity
    {
        $filtered = array_filter(
            iterator_to_array($this->storage),
            $callback
        );
        
        return !empty($filtered) ? current($filtered) : null;
    }

    public function contains(AbstractEntity $customer): bool
    {
        return $this->storage->contains($customer);
    }

    public function clear(): void
    {
        $this->storage = new SplObjectStorage();
    }

    public function toArray(): array
    {
        return array_map(
            fn(AbstractEntity $entity) => $entity->toArray(),
            iterator_to_array($this->storage)
        );
    }

    public function count(): int
    {
        return $this->storage->count();
    }

    public function getIterator(): Traversable
    {
        return $this->storage;
    }

    public function map(callable $callback): array
    {
        return array_map(
            $callback,
            iterator_to_array($this->storage)
        );
    }

    abstract public function validate(AbstractEntity $entity): void;
}