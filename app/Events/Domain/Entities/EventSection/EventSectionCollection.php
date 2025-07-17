<?php


declare(strict_types=1);

namespace App\Events\Domain\Entities\EventSection;

use App\Common\Domain\AbstractCollection;
use App\Common\Domain\AbstractEntity;

class EventSectionCollection extends AbstractCollection
{
    public function validate(AbstractEntity $entity): void
    {
        if (!$entity instanceof EventSection) {
            throw new \InvalidArgumentException('The entity must be an instance of EventSection');
        }
    }
}