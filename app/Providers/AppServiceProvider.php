<?php

namespace App\Providers;

use App\Http\Resources\ReservationResource;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
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
        if ($this->app->isLocal()) {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        ReservationResource::withoutWrapping();

        if (App::environment(['testing'])) {
            ini_set('memory_limit', '2G');
        }

        if (App::environment(['beta'])) {
            Mail::alwaysTo(config('mail.admin.address'));
        }
    }
}
