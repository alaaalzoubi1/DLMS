<?php

namespace App\Providers;

use Database\Factories\Doctor_AccountFactory;
use Database\Factories\DoctorFactory;
use Database\Factories\Subscriber_DoctorFactory;
use Database\Factories\SubscriberFactory;
use Database\Factories\UserFactory;
use Faker\Factory;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
//        $this->app->bind(Factory::class, function ($app) {
//            return new Factory($app['db']);
//        });
//
//        $this->app->register(\Database\Factories\SubscriberFactory::class);
//        $this->app->register(\Database\Factories\UserFactory::class);
//        $this->app->register(\Database\Factories\DoctorFactory::class);
//        $this->app->register(\Database\Factories\SubscriberDoctorFactory::class);
//        $this->app->register(\Database\Factories\DoctorAccountFactory::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
