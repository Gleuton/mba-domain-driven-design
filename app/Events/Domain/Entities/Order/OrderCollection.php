<?php


declare(strict_types=1);

namespace App\Events\Domain\Entities\Order;

use App\Common\Domain\AbstractCollection;
use App\Common\Domain\AbstractEntity;

class OrderCollection extends AbstractCollection
{
    public function validate(AbstractEntity $entity): void
    {
        if (!$entity instanceof Order) {
            throw new \InvalidArgumentException('The entity must be an instance of Order');
        }
    }
}