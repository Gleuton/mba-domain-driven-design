<?php


declare(strict_types=1);

namespace App\Events\Domain\Entities;

use SplObjectStorage;
use Traversable;

class EventSpotCollection implements \Countable, \IteratorAggregate
{
    private SplObjectStorage $spots;

    public function __construct()
    {
        $this->spots = new SplObjectStorage();
    }

    public function add(EventSpot $section): self
    {
        if (!$this->contains($section)) {
            $this->spots->attach($section);
        }
        return $this;
    }

    public function remove(EventSpot $section): self
    {
        $this->spots->detach($section);
        return $this;
    }

    public function contains(EventSpot $section): bool
    {
        return $this->spots->contains($section);
    }

    public function clear(): void
    {
        $this->spots = new SplObjectStorage();
    }

    public function toArray(): array
    {
        $formattedSpots = [];
        foreach ($this->spots as $spot) {
            $formattedSpots[] = $spot->toArray();
        }
        return $formattedSpots;
    }

    public function count(): int
    {
        return $this->spots->count();
    }

    public function getIterator(): Traversable
    {
        return $this->spots;
    }
}