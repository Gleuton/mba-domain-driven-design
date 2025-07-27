<?php

namespace Tests\Unit\Domain\Entities\EventSection;

use App\Events\Domain\Entities\EventSection\EventSection;
use App\Events\Domain\Entities\EventSection\EventSectionId;
use App\Events\Domain\Entities\EventSpot\EventSpot;
use App\Events\Domain\Entities\EventSpot\EventSpotId;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class EventSectionTest extends TestCase
{
    public function testCreateEventSection(): void
    {
        $sectionId = new EventSectionId();
        $sectionData = [
            'id' => $sectionId,
            'name' => 'VIP Section',
            'description' => 'VIP section with the best view',
            'price' => 150.0,
            'totalSpots' => 10,
            'totalSpotsReserved' => 0,
            'isPublished' => false
        ];

        $section = EventSection::create($sectionData);
        
        $serialized = $section->toArray();
        
        $this->assertEquals($sectionId, $serialized['id']);
        $this->assertEquals('VIP Section', $serialized['name']);
        $this->assertEquals('VIP section with the best view', $serialized['description']);
        $this->assertEquals(150.0, $serialized['price']);
        $this->assertEquals(10, $serialized['total_spots']);
        $this->assertEquals(0, $serialized['total_spots_reserved']);
        $this->assertFalse($serialized['is_published']);
        $this->assertIsArray($serialized['event_spots']);
    }

    public function testCreateEventSectionWithDefaults(): void
    {
        $sectionData = [
            'name' => 'Standard Section',
        ];

        $section = EventSection::create($sectionData);
        
        $serialized = $section->toArray();
        
        $this->assertNotEmpty($serialized['id']);
        $this->assertEquals('Standard Section', $serialized['name']);
        $this->assertNull($serialized['description']);
        $this->assertEquals(0.0, $serialized['price']);
        $this->assertEquals(0, $serialized['total_spots']);
        $this->assertEquals(0, $serialized['total_spots_reserved']);
        $this->assertFalse($serialized['is_published']);
        $this->assertIsArray($serialized['event_spots']);
    }

    public function testAddSpot(): void
    {
        $section = EventSection::create([
            'name' => 'Test Section',
            'totalSpots' => 0
        ]);
        
        $spot = EventSpot::create();
        
        $section->addSpot($spot);
        
        $serialized = $section->toArray();
        $this->assertEquals(1, $serialized['total_spots']);
        $this->assertCount(1, $serialized['event_spots']);
    }
    
    public function testAddDuplicateSpot(): void
    {
        $section = EventSection::create([
            'name' => 'Test Section',
            'totalSpots' => 0
        ]);
        
        $spot = EventSpot::create();
        
        $section->addSpot($spot);
        
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Spot already exists in the section.');
        
        $section->addSpot($spot);
    }
    
    public function testInitializeEventSpots(): void
    {
        $section = EventSection::create([
            'name' => 'Test Section',
            'totalSpots' => 5
        ]);
        
        $section->initializeEventSpots();
        
        $serialized = $section->toArray();
        $this->assertCount(5, $serialized['event_spots']);
    }
    
    public function testChangePrice(): void
    {
        $section = EventSection::create([
            'name' => 'Test Section',
            'price' => 100.0
        ]);
        
        $section->changePrice(150.0);
        
        $this->assertEquals(150.0, $section->price());
    }
    
    public function testChangePriceWithNegativeValue(): void
    {
        $section = EventSection::create([
            'name' => 'Test Section',
            'price' => 100.0
        ]);
        
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Price cannot be negative.');
        
        $section->changePrice(-50.0);
    }
    
    public function testPublish(): void
    {
        $section = EventSection::create([
            'name' => 'Test Section',
            'isPublished' => false
        ]);
        
        $section->publish();
        
        $serialized = $section->toArray();
        $this->assertTrue($serialized['is_published']);
    }
    
    public function testPublishAlreadyPublished(): void
    {
        $section = EventSection::create([
            'name' => 'Test Section',
            'isPublished' => true
        ]);
        
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Section is already published.');
        
        $section->publish();
    }
    
    public function testUnpublish(): void
    {
        $section = EventSection::create([
            'name' => 'Test Section',
            'isPublished' => true
        ]);
        
        $section->unpublish();
        
        $serialized = $section->toArray();
        $this->assertFalse($serialized['is_published']);
    }
    
    public function testUnpublishNotPublished(): void
    {
        $section = EventSection::create([
            'name' => 'Test Section',
            'isPublished' => false
        ]);
        
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Section is not published.');
        
        $section->unpublish();
    }
    
    public function testPublishAll(): void
    {
        $section = EventSection::create([
            'name' => 'Test Section',
            'isPublished' => false,
            'totalSpots' => 2
        ]);
        
        $section->initializeEventSpots();
        $section->publishAll();
        
        $serialized = $section->toArray();
        $this->assertTrue($serialized['is_published']);
        
        foreach ($serialized['event_spots'] as $spot) {
            $this->assertTrue($spot['is_published']);
        }
    }
    
    public function testAllowReserveSpot(): void
    {
        $section = EventSection::create([
            'name' => 'Test Section',
            'isPublished' => true,
            'totalSpots' => 1
        ]);
        
        $spotId = new EventSpotId();
        $spot = EventSpot::create([
            'spotId' => $spotId->getValue(),
            'isPublished' => true
        ]);
        $section->addSpot($spot);
        
        $this->assertTrue($section->allowReserveSpot($spotId));
    }
    
    public function testAllowReserveSpotWhenSectionNotPublished(): void
    {
        $section = EventSection::create([
            'name' => 'Test Section',
            'isPublished' => false,
            'totalSpots' => 1
        ]);
        
        $spotId = new EventSpotId();
        $spot = EventSpot::create([
            'spotId' => $spotId->getValue(),
            'isPublished' => true
        ]);
        $section->addSpot($spot);
        
        $this->assertFalse($section->allowReserveSpot($spotId));
    }
    
    public function testAllowReserveSpotWhenSpotNotPublished(): void
    {
        $section = EventSection::create([
            'name' => 'Test Section',
            'isPublished' => true,
            'totalSpots' => 1
        ]);
        
        $spotId = new EventSpotId();
        $spot = EventSpot::create([
            'spotId' => $spotId->getValue(),
            'isPublished' => false
        ]);
        $section->addSpot($spot);
        
        $this->assertFalse($section->allowReserveSpot($spotId));
    }
    
    public function testAllowReserveSpotWhenSpotAlreadyReserved(): void
    {
        $section = EventSection::create([
            'name' => 'Test Section',
            'isPublished' => true,
            'totalSpots' => 1
        ]);
        
        $spotId = new EventSpotId();
        $spot = EventSpot::create([
            'spotId' => $spotId->getValue(),
            'isPublished' => true
        ]);
        $section->addSpot($spot);
        
        $spot->reserve();
        
        $this->assertFalse($section->allowReserveSpot($spotId));
    }
    
    public function testMarkSpotAsReserved(): void
    {
        $section = EventSection::create([
            'name' => 'Test Section',
            'isPublished' => true,
            'totalSpots' => 1,
            'totalSpotsReserved' => 0
        ]);
        
        $spotId = new EventSpotId();
        $spot = EventSpot::create([
            'spotId' => $spotId->getValue(),
            'isPublished' => true
        ]);
        $section->addSpot($spot);
        
        $section->markSpotAsReserved($spotId);
        
        $this->assertEquals(1, $section->totalSpotsReserved());
        $this->assertTrue($spot->isReserved());
    }
}