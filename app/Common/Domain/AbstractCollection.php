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
        return array_find((array) $this->storage, $callback);
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
        $formatedArray = [];
        foreach ($this->storage as $entity) {
            $formatedArray[] = $entity->toArray();
        }
        return $formatedArray;
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
        $result = [];
        foreach ($this->storage as $item) {
            $result[] = $callback($item);
        }
        return $result;
    }

    abstract public function validate(AbstractEntity $entity): void;
}