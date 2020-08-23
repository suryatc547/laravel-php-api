<?php

namespace App\Http\Middleware;

use Closure;

class Cors
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
        $origin = trim($request->header('origin'));
        $origins = ['http://localhost:8000', 'http://localhost:3000'];
        // if(in_array($origin, $origins)){
            return $next($request)
            ->header('Access-Control-Allow-Origin','*')
            ->header('Access-Control-Allow-Methods','GET, POST, PUT, PATCH, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers','X-Requested-With, Content-Type, X-Auth-Token, Authorization');
        // } return $next($request);
    }
}
