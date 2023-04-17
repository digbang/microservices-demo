<?php

namespace App\Providers;

use App\Actions\Contracts\EventPublisher;
use App\Actions\RabbitMQPublisher;
use Laravel\Sanctum\Sanctum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public $bindings = [
        EventPublisher::class => RabbitMQPublisher::class,
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Sanctum::ignoreMigrations();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Model::preventLazyLoading(! app()->isProduction());
    }
}
