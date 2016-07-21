<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

//API
$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function($api){
	//Authentication
	$api->post('register', 'App\Http\Controllers\AuthenticateController@register');

	$api->post('authenticate', 'App\Http\Controllers\AuthenticateController@authenticate');

	$api->post('refreshToken', 'App\Http\Controllers\AuthenticateController@refreshToken');

	//Geolocations
	$api->group([], function($api){
		$api->resource('geolocations', 'App\Http\Controllers\GeolocationController');
	});

	$api->group(['middleware' => 'api.auth'], function($api){
		$api->get('users', function(){
			return App\User::all();
		});

		$api->get('user', function(){
			try{
				$user = JWTAuth::parseToken()->toUser();

				if(!$user){
					return $this->response->errorNotFound('User not found');
				}
				else{
					return $user;
				}
			}
			catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $ex){
				return $this->response->error('token is invalid');
			}
			catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $ex){
				return $this->response->error('token expired');
			}
			catch (\Tymon\JWTAuth\Exceptions\TokenBlacklistedException $ex){
				return $this->response->error('token blacklisted');
			}
		});
	});
});

Route::auth();

Route::get('/home', 'HomeController@index');
