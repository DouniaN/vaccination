<?php

namespace App\Http\Middleware;

use Closure;
use Gate;

class Unite
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

        if(Gate::allows('unite')){

            return $next($request);
            }
            else{
            abort(403);
            }
    }
}
