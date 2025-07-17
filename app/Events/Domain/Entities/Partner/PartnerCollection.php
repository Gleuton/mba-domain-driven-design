<?php

namespace App\Events\Domain\Entities\Partner;

use App\Common\Domain\AbstractCollection;
use App\Common\Domain\AbstractEntity;

class PartnerCollection extends AbstractCollection
{
    public function validate(AbstractEntity $entity): void
    {
        if (!$entity instanceof Partner) {
            throw new \InvalidArgumentException('The entity must be an instance of Partner');
        }
    }
}