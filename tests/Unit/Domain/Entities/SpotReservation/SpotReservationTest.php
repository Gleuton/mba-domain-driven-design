<?php

namespace Tests\Unit\Domain\Entities\SpotReservation;

use App\Events\Domain\Entities\Customer\CustomerId;
use App\Events\Domain\Entities\EventSpot\EventSpotId;
use App\Events\Domain\Entities\SpotReservation;
use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;

class SpotReservationTest extends TestCase
{
    public function testCreateSpotReservation(): void
    {
        $reservationDate = new DateTimeImmutable('2021-07-26 13:00:00');
        $eventSpotId = new EventSpotId();
        $customerId = new CustomerId();
        
        $reservationData = [
            'eventSpotId' => $eventSpotId,
            'customerId' => $customerId,
            'reservedAt' => $reservationDate->format(DateTimeInterface::ATOM)
        ];

        $reservation = SpotReservation::create($reservationData);
        
        $serialized = $reservation->toArray();
        
        $this->assertEquals($eventSpotId, $serialized['event_spot_id']);
        $this->assertEquals($customerId, $serialized['customer_id']);
        $this->assertEquals($reservationDate->format(DateTimeInterface::ATOM), $serialized['reservation_date']);
    }

    public function testCreateSpotReservationWithDefaultDate(): void
    {
        $before = new DateTimeImmutable();

        $eventSpotId = new EventSpotId();
        $customerId = new CustomerId();

        $reservationData = [
            'eventSpotId' => $eventSpotId,
            'customerId' => $customerId
        ];

        $reservation = SpotReservation::create($reservationData);

        $after = new DateTimeImmutable();

        $serialized = $reservation->toArray();

        $this->assertEquals($eventSpotId, $serialized['event_spot_id']);
        $this->assertEquals($customerId, $serialized['customer_id']);

        $reservationDate = DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $serialized['reservation_date']);

        $this->assertGreaterThanOrEqual($before->getTimestamp(), $reservationDate->getTimestamp());
        $this->assertLessThanOrEqual($after->getTimestamp(), $reservationDate->getTimestamp());
    }
}