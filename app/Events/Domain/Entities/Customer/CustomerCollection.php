<?php


declare(strict_types=1);

namespace App\Events\Domain\Entities\Customer;

use App\Common\Domain\AbstractCollection;
use App\Common\Domain\AbstractEntity;
use InvalidArgumentException;

class CustomerCollection extends AbstractCollection
{
    public function validate(AbstractEntity $entity):void
    {
        if (!$entity instanceof Customer) {
            throw new InvalidArgumentException('The entity must be an instance of Customer');
        }
    }
}