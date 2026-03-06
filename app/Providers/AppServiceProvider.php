<?php

namespace App\Providers;

use App\Models\RhContrat;
use App\Models\RhContratTerminaison;
use App\Observers\RH\RhContratObserver;
use App\Observers\RhContratTerminaisonObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        RhContratTerminaison::observe(RhContratTerminaisonObserver::class);
        //RhContrat::observe(RhContratObserver::class);
    }
}
