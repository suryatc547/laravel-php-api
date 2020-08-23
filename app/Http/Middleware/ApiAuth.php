<?php

namespace App\Http\Middleware;

use Closure;
use App\User;

class ApiAuth
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
        $token = '';
        if($request->isMethod('post') || $request->isMethod('put') || $request->isMethod('delete')){
            $token = @explode(' ', trim($request->header('Authorization')))[1];
        } else $token = $request->input('api_token');
        // $token = $request->input('api_token');
        if(!empty($token)){
            $valid = User::where('api_token','=',$token)->count();
            if($valid<=0) return response()->json(['code'=>401,'message'=>'Invalid token !','data'=>NULL]);
        } else {
            return response()->json(['code'=>401,'message'=>'Unauthorized !','data'=>NULL]);
        }
        return $next($request);
    }
}
