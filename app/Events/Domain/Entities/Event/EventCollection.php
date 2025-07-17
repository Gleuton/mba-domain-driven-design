<?php


declare(strict_types=1);

namespace App\Events\Domain\Entities\Event;

use App\Events\Domain\Entities\EventSpot\EventSpot;
use SplObjectStorage;
use Traversable;

class EventCollection implements \Countable, \IteratorAggregate
{
    private SplObjectStorage $event;

    public function __construct()
    {
        $this->event = new SplObjectStorage();
    }

    public function add(EventSpot $section): self
    {
        if (!$this->contains($section)) {
            $this->event->attach($section);
        }
        return $this;
    }

    public function remove(EventSpot $section): self
    {
        $this->event->detach($section);
        return $this;
    }

    public function contains(EventSpot $section): bool
    {
        return $this->event->contains($section);
    }

    public function clear(): void
    {
        $this->event = new SplObjectStorage();
    }

    public function toArray(): array
    {
        $formattedSpots = [];
        foreach ($this->event as $spot) {
            $formattedSpots[] = $spot->toArray();
        }
        return $formattedSpots;
    }

    public function count(): int
    {
        return $this->event->count();
    }

    public function getIterator(): Traversable
    {
        return $this->event;
    }
}