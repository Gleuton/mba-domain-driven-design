<?php

namespace App\Events\Domain\Entities\Partner;

use SplObjectStorage;
use Traversable;

class PartnerCollection implements \Countable, \IteratorAggregate
{
    private SplObjectStorage $partners;

    public function __construct()
    {
        $this->partners = new SplObjectStorage();
    }

    public function add(Partner $partner): self
    {
        if (!$this->contains($partner)) {
            $this->partners->attach($partner);
        }
        return $this;
    }

    public function remove(Partner $partner): self
    {
        $this->partners->detach($partner);
        return $this;
    }

    public function contains(Partner $section): bool
    {
        return $this->partners->contains($section);
    }

    public function clear(): void
    {
        $this->partners = new SplObjectStorage();
    }

    public function toArray(): array
    {
        $formattedPartners = [];
        foreach ($this->partners as $partner) {
            $formattedPartners[] = $partner->toArray();
        }
        return $formattedPartners;
    }

    public function count(): int
    {
        return $this->partners->count();
    }

    public function getIterator(): Traversable
    {
        return $this->partners;
    }
}