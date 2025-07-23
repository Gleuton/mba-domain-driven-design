<?php


declare(strict_types=1);

namespace App\Events\Domain\Entities\Event;

use App\Common\Domain\AbstractCollection;
use App\Common\Domain\AbstractEntity;

use App\Events\Domain\Entities\EventSection\EventSection;
use App\Events\Domain\Entities\EventSection\EventSectionId;
use InvalidArgumentException;
class EventCollection extends AbstractCollection
{
    public function validate(AbstractEntity $entity):void
    {
        if (!$entity instanceof Event) {
            throw new InvalidArgumentException('The entity must be an instance of Event');
        }
    }
}