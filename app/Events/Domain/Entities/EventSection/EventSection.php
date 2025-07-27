<?php

namespace App\Events\Domain\Entities\EventSection;

use App\Common\Domain\AbstractEntity;
use App\Common\Domain\ValueObjects\Name;
use App\Events\Domain\Entities\EventSpot\EventSpot;
use App\Events\Domain\Entities\EventSpot\EventSpotCollection;
use App\Events\Domain\Entities\EventSpot\EventSpotId;
use InvalidArgumentException;

class EventSection extends AbstractEntity
{
    private function __construct(
        private readonly EventSectionId $id,
        private readonly Name $name,
        private readonly ?string $description,
        private float $price,
        private readonly EventSpotCollection $eventSpots,
        private bool $isPublished,
        private int $totalSpots,
        private int $totalSpotsReserved,
    ) {
    }

    /**
     * @param array{
     *     id?: string|null,
     *     name: string,
     *     description?: string|null,
     *     price?: float,
     *     totalSpots: int,
     *     totalSpotsReserved?: int,
     *     isPublished?: bool
     * } $command
     * @throws InvalidArgumentException
     */
    public static function create(array $command): self
    {
        $eventSpots = new EventSpotCollection();
        return new self(
            new EventSectionId($command['id'] ?? null),
            new Name($command['name']),
            $command['description'] ?? null,
            (float) ($command['price'] ?? 0.0),
            $eventSpots,
            $command['isPublished'] ?? false,
            (int) ($command['totalSpots'] ?? 0),
            (int) ($command['totalSpotsReserved'] ?? 0)
        );
    }

    public function price(): float
    {
        return $this->price;
    }

    public function totalSpots(): int
    {
        return $this->totalSpots;
    }

    public function totalSpotsReserved(): int
    {
        return $this->totalSpotsReserved;
    }

    public function markSpotAsReserved(EventSpotId $eventSpotId): void
    {
        $spot = $this->spots()->spotById($eventSpotId);
        $spot->reserve();
        $this->totalSpotsReserved++;
    }

    public function equals(EventSectionId $param): bool
    {
        return $this->id->equals($param);
    }

    public function allowReserveSpot(EventSpotId $spotId): bool
    {
        if (!$this->isPublished) {
            return false;
        }

        $spot = $this->eventSpots->spotById($spotId);

        if ($spot->isReserved()) {
            return false;
        }

        if (!$spot->isPublished()) {
            return false;
        }

        return true;
    }

    public function addSpot(EventSpot $eventSpot): void
    {
        if ($this->eventSpots->contains($eventSpot)) {
            throw new InvalidArgumentException('Spot already exists in the section.');
        }
        $this->eventSpots->add($eventSpot);
        $this->totalSpots++;
    }

    public function initializeEventSpots(): void
    {
        for ($i = 0; $i < ($this->totalSpots ?? 0); $i++) {
            $this->eventSpots->add(EventSpot::create());
        }
    }

    public function changePrice(float $price): void
    {
        if ($price < 0) {
            throw new InvalidArgumentException('Price cannot be negative.');
        }
        $this->price = $price;
    }

    public function publishAll(): void
    {
        $this->publish();

        /** @var EventSpot $spot */
        foreach ($this->eventSpots as $spot) {
            $spot->publish();
        }
    }

    public function publish(): void
    {
        if ($this->isPublished) {
            throw new \RuntimeException('Section is already published.');
        }
        $this->isPublished = true;
    }

    public function unpublish(): void
    {
        if (!$this->isPublished) {
            throw new \RuntimeException('Section is not published.');
        }
        $this->isPublished = false;
    }

    public function spots(): EventSpotCollection
    {
        return $this->eventSpots;
    }

    protected function serializableFields(): array
    {
        return [
            'id' => $this->id->getValue(),
            'name' => $this->name->getValue(),
            'description' => $this->description,
            'price' => $this->price,
            'is_published' => $this->isPublished,
            'total_spots' => $this->totalSpots,
            'total_spots_reserved' => $this->totalSpotsReserved,
            'event_spots' => $this->eventSpots->toArray(),
        ];
    }
}