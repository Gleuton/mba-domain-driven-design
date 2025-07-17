<?php


declare(strict_types=1);

namespace App\Events\Domain\Entities\Customer;

use App\Events\Domain\Entities\EventSection\EventSection;
use SplObjectStorage;
use Traversable;

class CustomerCollection implements \Countable, \IteratorAggregate
{
    private SplObjectStorage $customers;

    public function __construct()
    {
        $this->customers = new SplObjectStorage();
    }

    public function add(EventSection $customer): self
    {
        if (!$this->contains($customer)) {
            $this->customers->attach($customer);
        }
        return $this;
    }

    public function remove(EventSection $customer): self
    {
        $this->customers->detach($customer);
        return $this;
    }

    public function contains(EventSection $customer): bool
    {
        return $this->customers->contains($customer);
    }

    public function clear(): void
    {
        $this->customers = new SplObjectStorage();
    }

    public function toArray(): array
    {
        $formatedArray = [];
        foreach ($this->customers as $customer) {
            $formatedArray[] = $customer->toArray();
        }
        return $formatedArray;
    }

    public function count(): int
    {
        return $this->customers->count();
    }

    public function getIterator(): Traversable
    {
        return $this->customers;
    }
}