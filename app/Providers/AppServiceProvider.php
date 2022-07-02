<?php

namespace App\Providers;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\App;
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
        if (App::environment(['testing'])) {
            ini_set('memory_limit', '2G');
        }
        /*Builder::macro('search', function ($field, $string) {
            // @phpstan-ignore-next-line
            return $string ? $this->where($field, 'like', '%'.$string.'%') : $this;
        });*/
    }
}
