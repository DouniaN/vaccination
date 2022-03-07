<?php

namespace App\Http\Middleware;

use Closure;
use Gate;

class SuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(Gate::allows('superAdmin')){

        return $next($request);
        }
        else{
        abort(403);
        }
    }
}
