<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Doctor_Account;
use App\Models\Order;
use App\Models\Subscriber;
use App\Policies\DoctorAccountPolicy;
use App\Policies\OrderPolicy;
use App\Policies\SubscriberPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Order::class => OrderPolicy::class,
        Subscriber::class => SubscriberPolicy::class,
        Doctor_Account::class => DoctorAccountPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
