<?php

namespace App\Events\Domain\Entities\EventSection;

use App\Common\Domain\AbstractEntity;
use App\Common\Domain\ValueObjects\Name;
use App\Events\Domain\Entities\EventSpot\EventSpot;
use App\Events\Domain\Entities\EventSpot\EventSpotCollection;
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
        private readonly int $totalSpots,
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
        $eventSpots = self::initializeEventSpots($command['totalSpots']);
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


    public function totalSpots(): int
    {
        return $this->totalSpots;
    }

    public function totalSpotsReserved(): int
    {
        return $this->totalSpotsReserved;
    }

    public function reserveSpot(): void
    {
        if ($this->totalSpotsReserved >= $this->totalSpots) {
            throw new \RuntimeException('No more spots available to reserve.');
        }

        $this->totalSpotsReserved++;
    }

    /**
     * @param $totalSpots
     * @return EventSpotCollection
     */
    private static function initializeEventSpots($totalSpots): EventSpotCollection
    {
        $eventSpots = new EventSpotCollection();
        for ($i = 0; $i < ($totalSpots ?? 0); $i++) {
            $eventSpots->add(EventSpot::create());
        }
        return $eventSpots;
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