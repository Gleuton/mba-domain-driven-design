<?php

namespace App\Providers;

use App\Common\Application\UnitOfWorkInterface;
use App\Common\Infra\UnitOfWorkEloquent;
use App\Events\Domain\Entities\Customer\Customer;
use App\Events\Domain\Entities\Event\Event;
use App\Events\Domain\Entities\Order\Order;
use App\Events\Domain\Entities\Partner\Partner;
use App\Events\Domain\Entities\SpotReservation;
use App\Events\Infra\Repository\CustomerRepository;
use App\Events\Infra\Repository\EventRepository;
use App\Events\Infra\Repository\OrderRepository;
use App\Events\Infra\Repository\PartnerRepository;
use App\Events\Infra\Repository\SpotReservationRepository;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(UnitOfWorkInterface::class, function ($app) {
            $unitOfWork = new UnitOfWorkEloquent($app->make(DatabaseManager::class));

            $unitOfWork->registerRepository(Customer::class, new CustomerRepository());
            $unitOfWork->registerRepository(Event::class, new EventRepository());
            $unitOfWork->registerRepository(Partner::class, new PartnerRepository());
            $unitOfWork->registerRepository(SpotReservation::class, new SpotReservationRepository());
            $unitOfWork->registerRepository(Order::class, new OrderRepository());

            return $unitOfWork;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
    }
}
