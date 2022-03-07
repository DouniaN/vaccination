<?php

namespace App\Http\Middleware;

use Closure;
use Gate;

class Admin
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

        if(Gate::allows('admin')){

            return $next($request);
            }
            else{
            abort(403);
            }
    }
}
