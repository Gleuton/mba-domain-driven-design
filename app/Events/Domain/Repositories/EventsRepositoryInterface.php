<?php

namespace App\Events\Domain\Repositories;

use App\Events\Domain\Entities\Event\Event;
use App\Events\Domain\Entities\Event\EventCollection;
use App\Events\Domain\Entities\Event\EventId;

interface EventsRepositoryInterface
{
    public function save(Event $entity): void;

    public function findById(EventId $id): Event;

    public function findAll(): EventCollection;

    public function remove(Event $entity): void;
}