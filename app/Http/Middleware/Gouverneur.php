<?php

namespace App\Http\Middleware;

use Closure;
use Gate;

class Gouverneur
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

        if(Gate::allows('gouverneur')){

            return $next($request);
            }
            else{
            abort(403);
            }
    }
}
