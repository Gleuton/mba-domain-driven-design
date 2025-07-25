<?php

namespace App\Events\Application;

use App\Common\Infra\UnitOfWorkEloquent;
use App\Events\Domain\Entities\Partner\Partner;
use App\Events\Domain\Entities\Partner\PartnerCollection;
use App\Events\Infra\Repository\PartnerRepository;

readonly class PartnerService
{
    public function __construct(
        private PartnerRepository $eventRepository,
        private UnitOfWorkEloquent $unitOfWork
    )
    {
    }

    public function list(): PartnerCollection
    {
        return $this->eventRepository
            ->findAll();
    }

    public function register(array $input): array
    {
        $partner = Partner::create($input);

        $this->unitOfWork->register($partner);
        $this->unitOfWork->commit();

        return $partner->toArray();
    }

    public function update(array $input): array
    {
        $partner = $this->eventRepository->findById($input['id']);

        $partner->changeName($input['name']);

        $this->unitOfWork->register($partner);
        $this->unitOfWork->commit();

        return $partner->toArray();
    }
}