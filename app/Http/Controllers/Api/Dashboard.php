<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\User;

class Dashboard extends Controller
{
    public function test(){
    	echo 'hi';
    }

    private function getToken($request){
    	return @explode(' ', trim($request->header('Authorization')))[1];
    }

    public function getUserData(Request $request){
    	$resp = ['code'=>200,'message'=>'error','data'=>NULL];
    	$token = $this->getToken($request);
    	$userdetail = User::where('api_token',$token)->select('name','email','profile','phone')->first();
    	if(@$userdetail) {
    		$resp['message'] = 'success'; $resp['data'] = $userdetail;
    	} else $resp['message'] = 'No such user found!';
    	return response()->json($resp);
    }
}
