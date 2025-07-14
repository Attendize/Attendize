<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Organiser;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // inject organisers into Master layout
        View::composer('Shared.Layouts.Master', function ($view) {
            $view->with('organisers', Organiser::all());
        });
    }
    
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Only load LaravelIdeHelper if we're in development mode
        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
    }
}
