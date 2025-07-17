<?php

namespace App\Events\Domain\Repositories;

use App\Events\Domain\Entities\Partner\Partner;
use App\Events\Domain\Entities\Partner\PartnerCollection;
use App\Events\Domain\Entities\Partner\PartnerId;

interface PartnerRepositoryInterface
{
    public function save(Partner $entity): void;

    public function findById(PartnerId $id): Partner;

    public function findAll(): PartnerCollection;

    public function remove(Partner $entity): void;
}