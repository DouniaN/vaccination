<?php

namespace App\Http\Middleware;

use Closure;
use Gate;

class Root
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

        if(Gate::allows('root')){

            return $next($request);
            }
            else{
            abort(403);
            }
    }
}
