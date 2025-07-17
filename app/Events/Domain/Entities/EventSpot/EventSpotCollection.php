<?php


declare(strict_types=1);

namespace App\Events\Domain\Entities\EventSpot;

use SplObjectStorage;
use Traversable;

class EventSpotCollection implements \Countable, \IteratorAggregate
{
    private SplObjectStorage $spots;

    public function __construct()
    {
        $this->spots = new SplObjectStorage();
    }

    public function add(EventSpot $eventSpot): self
    {
        if (!$this->contains($eventSpot)) {
            $this->spots->attach($eventSpot);
        }
        return $this;
    }

    public function remove(EventSpot $eventSpot): self
    {
        $this->spots->detach($eventSpot);
        return $this;
    }

    public function contains(EventSpot $eventSpot): bool
    {
        return $this->spots->contains($eventSpot);
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