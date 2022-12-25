<?php

namespace App\Providers;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    public function boot()
    {
        $this->registerPolicies();

        Gate::before(function ($user, $ability) {
            if (in_array(1, auth()->user()->roles->pluck('id')->toArray())) {
                return true;
            }
        });

        Permission::with('roles')
        ->get()
        ->each(function($permission) {
            Gate::define($permission->key, function(User $user) use ($permission) {
                return $user->roles->intersect($permission->roles)->count();
            });
        });
    }
}
