<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Gate::define('root',function($user){
            if(Auth::user()->role->role=="root"){
                return true;
            }
            else{
                return false;
            }
         });
        Gate::define('superAdmin_root',function($user){
            if(Auth::user()->role->role=="superAdmin" || Auth::user()->role->role=="root" ){
                return true;
            }
            else{
                return false;
            }
         });
         Gate::define('superAdmin',function($user){
            if(Auth::user()->role->role=="superAdmin" ){
                return true;
            }
            else{
                return false;
            }
         });
         Gate::define('admin',function($user){
            if(Auth::user()->role->role=="admin"){
                return true;
            }
            else{
                return false;
            }
         });
         Gate::define('gouverneur',function($user){
            if(Auth::user()->role->role=="gouverneur"){
                return true;
            }
            else{
                return false;
            }
         });
         Gate::define('unite',function($user){
            if(Auth::user()->role->role=="unite"){
                return true;
            }
            else{
                return false;
            }
         });

        //
    }
}
