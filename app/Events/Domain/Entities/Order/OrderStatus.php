<?php

namespace App\Events\Domain\Entities\Order;

enum OrderStatus: string
{
    case PENDING = 'Pending';
    case PAID = 'Paid';
    Case CANCELLED = 'Cancelled';
}
