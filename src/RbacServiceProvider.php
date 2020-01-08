<?php

namespace Cosmos\Rbac;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;

class RbacServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/permission.php' => config_path('rbac.php'),
        ], 'config');

        $this->bladeDirectives();
    }

    private function bladeDirectives()
    {
        Blade::if('role', function ($name) {
            if (!Auth::check()) return false;
            $user = \App\User::find(Auth::user()->id);
            return $user->hasRole($name);
        });

        Blade::if('permission', function ($name) {
            if (!Auth::check()) return false;
            $user = \App\User::find(Auth::user()->id);
            return $user->hasPermission($name);
        });
    }
}
