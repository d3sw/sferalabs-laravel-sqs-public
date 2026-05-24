<?php

namespace Deluxe\SqsRaw;

use Deluxe\SqsRaw\Sqs\Connector;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobProcessed;

class SqsRawServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/sqs-raw.php' => config_path('sqs-raw.php')
        ]);

        Queue::after(static function (JobProcessed $event) {
            if (!$event->job->isDeletedOrReleased()) {
                $event->job->delete();
            }
        });
    }

    /**
     * @return void
     */
    public function register()
    {
        $this->app->booted(function () {
            $this->app['queue']->extend('sqs-raw', function () {
                return new Connector();
            });
        });
    }
}
