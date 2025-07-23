<?php

namespace App\Events\Domain\Entities\Event;

use App\Common\Domain\AbstractEntity;
use App\Common\Domain\AggregateRoot;
use App\Common\Domain\ValueObjects\Name;
use App\Common\Domain\ValueObjects\Uuid;
use App\Events\Domain\Entities\EventSection\EventSection;
use App\Events\Domain\Entities\EventSection\EventSectionCollection;
use App\Events\Domain\Entities\EventSection\EventSectionId;
use App\Events\Domain\Entities\EventSpot\EventSpotId;
use App\Events\Domain\Entities\Partner\PartnerId;
use DateTimeImmutable;
use Exception;
use InvalidArgumentException;

class Event extends AggregateRoot
{
    private bool $isPublished = false;
    private int $totalSpots = 0;
    private int $totalSpotsReserved = 0;

    private function __construct(
        private readonly EventId $id,
        private Name $name,
        private ?string $description,
        private DateTimeImmutable $date,
        private readonly PartnerId $partnerId,
        private readonly EventSectionCollection $eventSections
    ) {
    }

    /**
     * @param array{
     *     id?: string|null,
     *     name: string,
     *     description?: string|null,
     *     date: string,
     *     partnerId?: string|null,
     * } $command
     * @throws Exception
     */
    public static function create(array $command): self
    {
        return new self(
            new EventId($command['id'] ?? null),
            new Name($command['name']),
            $command['description'] ?? null,
            new DateTimeImmutable($command['date']),
            new PartnerId($command['partnerId'] ?? null),
            new EventSectionCollection(),
        );
    }

    public function publishAll(): void
    {
        $this->publish();

        /** @var EventSection $section */
        foreach ($this->eventSections as $section) {
            $section->publishAll();
        }
    }
    public function allowReserveSpots(EventSectionId $sectionId, EventSpotId $spotId): bool
    {
        if (!$this->isPublished) {
            return false;
        }
        /**
         * @var EventSection $section
         */
        $section = $this->eventSections->find(
            fn(EventSection $section) => $section->equals($sectionId)
        );
        if (!$section) {
            throw new InvalidArgumentException('Event section not found');
        }

        return $section->allowReserveSpot($spotId);
    }

    public function publish(): void
    {
        $this->isPublished = true;
    }

    public function unpublish(): void
    {
        $this->isPublished = false;
    }

    public function totalSpots(): int
    {
        return $this->totalSpots;
    }

    public function addSection(EventSection $section): void
    {
        $this->eventSections->add($section);
        $this->totalSpots += $section->totalSpots();
        $this->totalSpotsReserved += $section->totalSpotsReserved();
    }

    public function changeName(Name $name): void
    {
        $this->name = $name;
    }

    public function changeDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function changeDate(DateTimeImmutable $date): void
    {
        $this->date = $date;
    }

    public function sections(): EventSectionCollection
    {
        return $this->eventSections;
    }

    public function sectionById(EventSectionId $sectionId): EventSection
    {
        return $this->eventSections->sectionById($sectionId);
    }

    public function markSpotAsReserved(EventSectionId $sectionId, EventSpotId $eventSpotId): void
    {
        $section = $this->sectionById($sectionId);
        $section->markSpotAsReserved($eventSpotId);
        $this->totalSpotsReserved++;
    }

    protected function serializableFields(): array
    {
        return [
            'id' => $this->id->getValue(),
            'name' => $this->name->getValue(),
            'description' => $this->description,
            'date' => $this->date->format('Y-m-d H:i:s'),
            'partner_id' => $this->partnerId->getValue(),
            'is_published' => $this->isPublished,
            'total_spots' => $this->totalSpots,
            'total_spots_reserved' => $this->totalSpotsReserved,
            'event_sections' => $this->eventSections->toArray(),
        ];
    }
}