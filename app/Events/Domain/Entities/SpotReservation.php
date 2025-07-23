<?php

namespace App\Events\Domain\Entities;

use App\Common\Domain\AggregateRoot;
use App\Events\Domain\Entities\Customer\CustomerId;
use App\Events\Domain\Entities\EventSpot\EventSpotId;
use \DateMalformedStringException;
use DateTimeImmutable;
use DateTimeInterface;

class SpotReservation extends AggregateRoot
{
    private function __construct(
        private EventSpotId $eventSpotId,
        private CustomerId $customerId,
        private DateTimeImmutable $reservedAt,
    )
    {
    }

    /**
     * @param ?array{
     *     eventSpotId: string,
     *     customerId: string,
     *     reservedAt: string
     * } $data
     * @throws DateMalformedStringException
     */
    public static function create(?array $data = []): self
    {
        return new self(
            new EventSpotId($data['eventSpotId']),
            new CustomerId($data['customerId']),
            new DateTimeImmutable($data['reservedAt'])
        );
    }

    protected function serializableFields(): array
    {
        return [
            'event_spot_id' => $this->eventSpotId->getValue(),
            'customer_id' => $this->customerId,
            'reservedAt' => $this->reservedAt->format(DateTimeInterface::ATOM),
        ];
    }
}