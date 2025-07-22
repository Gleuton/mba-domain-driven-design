<?php

namespace App\Common\Infra;

use App\Common\Domain\AbstractCollection;
use App\Common\Domain\AbstractEntity;
use App\Common\Domain\ValueObjects\Uuid;

interface RepositoryInterface
{
    public function save(AbstractEntity $entity): void;

    public function findById(Uuid $id): AbstractEntity;

    public function findAll(): AbstractCollection;

    public function remove(AbstractEntity $entity): void;
}