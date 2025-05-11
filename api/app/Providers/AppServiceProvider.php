<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(\App\Repositories\Interfaces\FlightTicketRepositoryInterface::class, \App\Repositories\FlightTicketRepository::class);
        $this->app->bind(\App\Services\FlightTicketServiceInterface::class, \App\Services\FlightTicketService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
