<?php

namespace Tests\Unit\Application;

use App\Common\Application\UnitOfWorkInterface;
use App\Events\Application\OrderService;
use App\Events\Domain\Entities\Customer\Customer;
use App\Events\Domain\Entities\Customer\CustomerId;
use App\Events\Domain\Entities\Event\Event;
use App\Events\Domain\Entities\Event\EventId;
use App\Events\Domain\Entities\EventSection\EventSection;
use App\Events\Domain\Entities\EventSection\EventSectionId;
use App\Events\Domain\Entities\EventSpot\EventSpotId;
use App\Events\Domain\Entities\Order\Order;
use App\Events\Domain\Entities\Order\OrderCollection;
use App\Events\Domain\Entities\SpotReservation;
use App\Events\Infra\Repository\CustomerRepository;
use App\Events\Infra\Repository\EventRepository;
use App\Events\Infra\Repository\OrderRepository;
use App\Events\Infra\Repository\SpotReservationRepository;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class OrderServiceTest extends TestCase
{
    private OrderRepository|MockObject $orderRepository;
    private CustomerRepository|MockObject $customerRepository;
    private EventRepository|MockObject $eventRepository;
    private SpotReservationRepository|MockObject $spotReservationRepository;
    private UnitOfWorkInterface|MockObject $unitOfWork;
    private OrderService $orderService;

    protected function setUp(): void
    {
        $this->orderRepository = $this->createMock(OrderRepository::class);
        $this->customerRepository = $this->createMock(CustomerRepository::class);
        $this->eventRepository = $this->createMock(EventRepository::class);
        $this->spotReservationRepository = $this->createMock(SpotReservationRepository::class);
        $this->unitOfWork = $this->createMock(UnitOfWorkInterface::class);

        $this->orderService = new OrderService(
            $this->orderRepository,
            $this->customerRepository,
            $this->eventRepository,
            $this->spotReservationRepository,
            $this->unitOfWork
        );
    }

    public function testList(): void
    {
        $customerId = new CustomerId();
        $eventSpotId = new EventSpotId();
        
        $orderCollection = new OrderCollection();
        $orderCollection->add(Order::create([
            'customerId' => $customerId,
            'eventSpotId' => $eventSpotId,
            'amount' => 100.0
        ]));

        $this->orderRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn($orderCollection);

        $result = $this->orderService->list();

        $this->assertCount(1, $result);
    }

    public function testCreate(): void
    {
        $customerId = new CustomerId();
        $eventId = new EventId();
        $sectionId = new EventSectionId();
        $spotId = new EventSpotId();

        $customer = $this->createMock(Customer::class);
        $event = $this->createMock(Event::class);
        $section = $this->createMock(EventSection::class);

        $this->customerRepository
            ->expects($this->once())
            ->method('findById')
            ->with($this->equalTo($customerId))
            ->willReturn($customer);


        $this->eventRepository
            ->expects($this->once())
            ->method('findById')
            ->with($this->equalTo($eventId))
            ->willReturn($event);

        $this->spotReservationRepository
            ->expects($this->once())
            ->method('findById')
            ->with($this->equalTo($spotId))
            ->willReturn(null);

        $event->expects($this->once())
            ->method('allowReserveSpots')
            ->with($this->equalTo($sectionId), $this->equalTo($spotId))
            ->willReturn(true);

        $event->expects($this->once())
            ->method('sectionById')
            ->with($this->equalTo($sectionId))
            ->willReturn($section);

        $section->expects($this->once())
            ->method('price')
            ->willReturn(150.0);

        $event->expects($this->once())
            ->method('markSpotAsReserved')
            ->with($this->equalTo($sectionId), $this->equalTo($spotId));

        $this->unitOfWork->expects($this->exactly(3))
            ->method('register')
            ->willReturnCallback(function ($argument) use (&$callCount, $event) {
                $callCount++;

                match ($callCount) {
                    1 => $this->assertInstanceOf(SpotReservation::class, $argument),
                    2 => $this->assertInstanceOf(Order::class, $argument),
                    3 => $this->assertSame($event, $argument),
                    default => $this->fail("Unexpected call #{$callCount}")
                };
            });

        $this->unitOfWork->expects($this->once())
            ->method('commit');

        $input = [
            'event_id' => $eventId,
            'customer_id' => $customerId,
            'event_section_id' => $sectionId,
            'event_spot_id' => $spotId
        ];

        $result = $this->orderService->create($input);

        $this->assertEquals($spotId, $result->toArray()['event_spot_id']);
        $this->assertEquals($customerId, $result->toArray()['customer_id']);
    }

    public function testCreateWhenSpotReservationNotAllowed(): void
    {
        $customerId = new CustomerId();
        $eventId = new EventId();
        $sectionId = new EventSectionId();
        $spotId = new EventSpotId();

        $customer = $this->createMock(Customer::class);
        $event = $this->createMock(Event::class);

        $this->customerRepository
            ->expects($this->once())
            ->method('findById')
            ->with($this->equalTo($customerId))
            ->willReturn($customer);

        $this->eventRepository
            ->expects($this->once())
            ->method('findById')
            ->with($this->equalTo($eventId))
            ->willReturn($event);

        $event->expects($this->once())
            ->method('allowReserveSpots')
            ->with($this->equalTo($sectionId), $this->equalTo($spotId))
            ->willReturn(false);

        $input = [
            'event_id' => $eventId,
            'customer_id' => $customerId,
            'event_section_id' => $sectionId,
            'event_spot_id' => $spotId
        ];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Spot reservation is not allowed for this event or section');

        $this->orderService->create($input);
    }

    public function testCreateWhenSpotAlreadyReserved(): void
    {
        $customerId = new CustomerId();
        $eventId = new EventId();
        $sectionId = new EventSectionId();
        $spotId = new EventSpotId();

        $customer = $this->createMock(Customer::class);
        $event = $this->createMock(Event::class);
        $existingReservation = $this->createMock(SpotReservation::class);

        $this->customerRepository
            ->expects($this->once())
            ->method('findById')
            ->with($this->equalTo($customerId))
            ->willReturn($customer);

        $this->eventRepository
            ->expects($this->once())
            ->method('findById')
            ->with($this->equalTo($eventId))
            ->willReturn($event);

        $event->expects($this->once())
            ->method('allowReserveSpots')
            ->with($this->equalTo($sectionId), $this->equalTo($spotId))
            ->willReturn(true);

        $this->spotReservationRepository
            ->expects($this->once())
            ->method('findById')
            ->with($this->equalTo($spotId))
            ->willReturn($existingReservation);

        $input = [
            'event_id' => $eventId,
            'customer_id' => $customerId,
            'event_section_id' => $sectionId,
            'event_spot_id' => $spotId
        ];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Spot is already reserved');

        $this->orderService->create($input);
    }
}