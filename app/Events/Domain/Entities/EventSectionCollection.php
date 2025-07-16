<?php


declare(strict_types=1);

namespace App\Events\Domain\Entities;

use SplObjectStorage;
use Traversable;

class EventSectionCollection implements \Countable, \IteratorAggregate
{
    private SplObjectStorage $sections;

    public function __construct()
    {
        $this->sections = new SplObjectStorage();
    }

    public function add(EventSection $section): self
    {
        if (!$this->contains($section)) {
            $this->sections->attach($section);
        }
        return $this;
    }

    public function remove(EventSection $section): self
    {
        $this->sections->detach($section);
        return $this;
    }

    public function contains(EventSection $section): bool
    {
        return $this->sections->contains($section);
    }

    public function clear(): void
    {
        $this->sections = new SplObjectStorage();
    }

    public function toArray(): array
    {
        $formatedSections = [];
        foreach ($this->sections as $section) {
            $formatedSections[] = $section->toArray();
        }
        return $formatedSections;
    }

    public function count(): int
    {
        return $this->sections->count();
    }

    public function getIterator(): Traversable
    {
        return $this->sections;
    }
}