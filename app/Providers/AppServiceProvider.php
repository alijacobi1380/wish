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
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        function onesr($ticket)
        {
            $ticket->Files = unserialize($ticket->Files);
        }

        function manysr($tickets)
        {
            $tickets->map(function ($item) {
                $item->Files = unserialize($item->Files);
            });
        }
    }
}
