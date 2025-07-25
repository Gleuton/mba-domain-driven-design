<?php

namespace App\Events\Application;

use App\Common\Application\UnitOfWorkInterface;
use App\Events\Domain\Entities\Partner\Partner;
use App\Events\Domain\Entities\Partner\PartnerCollection;
use App\Events\Infra\Repository\PartnerRepository;
use Throwable;

readonly class PartnerService
{
    public function __construct(
        private PartnerRepository $eventRepository,
        private UnitOfWorkInterface $unitOfWork
    )
    {
    }

    public function list(): PartnerCollection
    {
        return $this->eventRepository
            ->findAll();
    }

    /**
     * @param array{
     *     name: string,
     * } $input
     * @return Partner
     * @throws Throwable
     */
    public function register(array $input): Partner
    {
        $partner = Partner::create($input);

        $this->unitOfWork->register($partner);
        $this->unitOfWork->commit();

        return $partner;
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