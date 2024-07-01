<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Jobs\UpdateVisitorTable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class CustomerAppMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $ip = get_visitor_IP();

        UpdateVisitorTable::dispatch($ip);        // update the visitor table for state

        return $next($request);
    }
}
