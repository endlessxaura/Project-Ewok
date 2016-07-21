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

Route::get('testMap', function(){
	return view('map', ['token' => JWTAuth::getToken()]);
});

//API
$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function($api){
	/////Authentication/////
	$api->post('register', 'App\Http\Controllers\AuthenticateController@register');
	$api->post('authenticate', 'App\Http\Controllers\AuthenticateController@authenticate');
	$api->post('refreshToken', 'App\Http\Controllers\AuthenticateController@refreshToken');

	/////Reading data/////

	//Geolocations
	$api->get('geolocations', 'App\Http\Controllers\GeolocationController@index');
	$api->get('geolocations/{id}', 'App\Http\Controllers\GeolocationController@show');

	/////Updating and deleting data/////
	$api->group(['middleware' => 'api.auth'], function($api){
		$api->post('geolocations', 'App\Http\Controllers\GeolocationController@store');
		$api->put('geolocations/{id}', 'App\Http\Controllers\GeolocationController@update');
		$api->delete('geolocations/{id}', 'App\Http\Controllers\GeolocationController@destroy');
	});

	//This is just my random bullshit
	// $api->group(['middleware' => 'api.auth'], function($api){
	// 	$api->get('users', function(){
	// 		return App\User::all();
	// 	});

	// 	$api->get('user', function(){
	// 		try{
	// 			$user = JWTAuth::parseToken()->toUser();

	// 			if(!$user){
	// 				return $this->response->errorNotFound('User not found');
	// 			}
	// 			else{
	// 				return $user;
	// 			}
	// 		}
	// 		catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $ex){
	// 			return $this->response->error('token is invalid');
	// 		}
	// 		catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $ex){
	// 			return $this->response->error('token expired');
	// 		}
	// 		catch (\Tymon\JWTAuth\Exceptions\TokenBlacklistedException $ex){
	// 			return $this->response->error('token blacklisted');
	// 		}
	// 	});
	// });
});

Route::auth();

Route::get('/home', 'HomeController@index');
