<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

use App\User;
use Validator;

class Users extends Controller
{
	public function register(Request $request){
		$resp = ['code'=>200,'message'=>NULL,'data'=>NULL];
		$post = $request->post();
		$rules['name'] = 'required|string|max:30|unique:users';
		$rules['email'] = 'required|email|unique:users';
		$rules['password'] = 'required|string|min:8|max:13';
		$rules['confirm'] = 'required|string|same:password';
		$rules['profile'] = 'mimes:png,jpg,jpeg';
		$validate = Validator::make($post,$rules);
		if($validate->fails()){
			$resp['message'] = 'Invalid data send!'; $resp['code'] = 400;
			$resp['data'] = $validate->messages();
		} else {
			$insData = [
				'name'=>$post['name'],
				'email'=>$post['email'],
				'password'=>Hash::make($post['password']),
				'api_token'=>Hash::make(\Str::random(10))
			];
			$profile = $request->file('profile');
    		if($profile){
    			$userId = $insData['api_token'];
    			$saveAs = $userId.'-'.time().'.'.$profile->extension();
    			$profile->storeAs('profiles',$saveAs);
    			$insData['profile'] = $saveAs;
    		}
			$ins = User::create($insData);
			if($ins){
				$resp['message'] = 'Registered successfully!'; $resp['data'] = ['api_token'=>$ins->api_token];
			} else {
				$resp['message'] = 'Unable to register, something went wrong!'; $resp['data'] = '';
			}
		}
		return response()->json($resp);
	}

    public function login(Request $request){
    	$resp = ['code'=>200,'message'=>NULL,'data'=>NULL];
		$post = $request->post();
		$rules['email'] = 'required|email';
		$rules['password'] = 'required|string';
		$validate = Validator::make($post,$rules);
		if($validate->fails()){
			$resp['message'] = 'Invalid data send!'; $resp['code'] = 400;
			$resp['data'] = $validate->messages();
		} else {
			$auth = User::where('email',$post['email'])->value('password');
			if(@Hash::check($post['password'],$auth)){
				$token = md5(time());
				$update = User::where('email',$post['email'])->update(['api_token'=>$token]);
				$resp['message'] = 'Logged in successfully!'; $resp['data'] = ['api_token'=>$token];
			} else {
				$resp['message'] = 'Invalid credentials'; $resp['data'] = ''; $resp['code'] = 401;
			}
		}
		return response()->json($resp);
    }
}
