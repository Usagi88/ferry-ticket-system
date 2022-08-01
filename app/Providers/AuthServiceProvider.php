<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        Vessel::class => VesselPolicy::class,
        Island::class => IslandPolicy::class,
        TicketType::class => TicketTypePolicy::class,
        Route::class => RoutePolicy::class,
        Schedule::class => SchedulePolicy::class,
        User::class => UserPolicy::class,
        Role::class => RolePolicy::class,
        Booking::class => BookingPolicy::class,
        VesselType::class => VesselTypePolicy::class,
        Profile::class => ProfilePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('isAdmin', function ($user) {//defining authorization
            return $user->roles->first()->slug == 'admin';
        });

        Gate::define('isStaff', function ($user) {
            return $user->roles->first()->slug == 'staff';
        });

        Gate::define('isMerchant', function ($user) {
            return $user->roles->first()->slug == 'merchant';
        });

        Gate::define('isAgent', function ($user) {
            return $user->roles->first()->slug == 'agent';
        });
    }
}
