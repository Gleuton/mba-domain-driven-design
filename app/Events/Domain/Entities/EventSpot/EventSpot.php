<?php

namespace App\Events\Domain\Entities\EventSpot;

use App\Common\Domain\AbstractEntity;
use DomainException;

class EventSpot extends AbstractEntity
{
    public function __construct(
        private readonly EventSpotId $spotId,
        private ?string $location,
        private bool $isReserved,
        private bool $isPublished,
    ) {
    }

    /**
     * @param ?array{
     *     spotId: string,
     *     location?: string|null,
     *     isReserved?: bool,
     *     isPublished?: bool
     * } $data
     */
    public static function create(?array $data = []): self
    {
        return new self(
            new EventSpotId($data['spotId'] ?? null),
            $data['location'] ?? null,
            $data['isReserved'] ?? false,
            $data['isPublished'] ?? false,
        );
    }

    public function publish(): void
    {
        if ($this->isPublished) {
            throw new DomainException('Spot is already published');
        }
        $this->isPublished = true;
    }

    public function unpublish(): void
    {
        if (!$this->isPublished) {
            throw new DomainException('Spot is not published');
        }
        $this->isPublished = false;
    }
    public function reserve(): void
    {
        if ($this->isReserved) {
            throw new DomainException('Spot is already reserved');
        }
        $this->isReserved = true;
    }
    public function changeLocation(?string $location): void
    {
        if ($this->isReserved) {
            throw new DomainException('Cannot change location of a reserved spot');
        }
        $this->location = $location;
    }

    public function serializableFields(): array
    {
        return [
            'spotId' => $this->spotId->getValue(),
            'location' => $this->location,
            'isReserved' => $this->isReserved,
            'isPublished' => $this->isPublished,
        ];
    }
}