<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

use App\User;

class Dashboard extends Controller
{
    public function test(){
    	echo 'hi';
    }

    private function getToken($request){
    	return $request->isMethod('get')?$request->input('api_token'):@explode(' ', trim($request->header('Authorization')))[1];
    }

    private function profileData($token){
    	return User::where('api_token',$token)->select('name','email','profile','phone')->first();
    }

    public function getUserData(Request $request){
    	$resp = ['code'=>200,'message'=>'error','data'=>NULL];
    	$token = $this->getToken($request);
    	$userdetail = $this->profileData($token);
    	if(@$userdetail) {
    		$resp['message'] = 'success'; $resp['data'] = $userdetail;
    	} else $resp['message'] = 'No such user found!';
    	return response()->json($resp);
    }

    public function updateUserData(Request $request){
    	$resp = ['code'=>200,'message'=>'error','data'=>NULL];
    	$token = $this->getToken($request);
    	$data = $request->all();
    	$rules['name'] = 'required|string|max:30';
    	$rules['phone'] = 'required|numeric';
    	$rules['profile'] = 'nullable|mimes:png,jpg,jpeg';
    	$validate = Validator::make($data,$rules);
    	if($validate->fails()){
    		$resp['message'] = 'Invalid data send!';
    		$resp['data'] = $validate->messages();
    	} else {
    		$update['name'] = $data['name'];
    		$update['phone'] = $data['phone'];
    		$profile = $request->file('profile');
    		if($profile){
    			$userId = User::where('api_token',$token)->value('_id');
    			$saveAs = $userId.'-'.time().'.'.$profile->extension();
    			$file->storeAs('profiles',$saveAs);
    			$update['profile'] = $saveAs;
    		}
    		$result = User::where('api_token',$token)->update($update);
    		// print_r($token); die;
    		if($result){
    			$resp['message'] = 'Profile updated successfully!';
    			$resp['data'] =  $this->profileData($token);
    		} else {
    			$resp['message'] = 'Unable to update profile, something went wrong!';
    		}
    	} return response()->json($resp);
    }

    public function deleteUserData(Request $request){
    	$resp = ['code'=>200,'message'=>'error','data'=>NULL];
    	$data = $request->all();
    	$token = $this->getToken($request);
    	$delete = User::where('api_token',$token)->delete();
    	if($delete){
    		$resp['message'] = 'Your account deleted successfully!';
    	} else {
    		$resp['message'] = 'Unable to delete your account, something went wrong!';
    	} return response()->json($resp);
    }
}
