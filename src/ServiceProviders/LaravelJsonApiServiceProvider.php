<?php

namespace Giadc\JsonApi\ServiceProviders;

use Illuminate\Support\ServiceProvider;

class LaravelJsonApiServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            'Giadc\JsonApi\Interfaces\ResponseContract',
            'Giadc\JsonApi\Responses\LaravelResponse'
        );

        $this->app->bind(
            'Giadc\JsonApi\Interfaces\AbstractJsonApiRepositoryInterface',
            'Giadc\JsonApi\Repositories\AbstractJsonApiDoctrineRepository'
        );
    }
}
