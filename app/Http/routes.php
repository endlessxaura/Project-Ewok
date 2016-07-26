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
session_start();
use Illuminate\Http\Request;


$dispatcher = app('Dingo\Api\Dispatcher');

Route::get('/', function () {
	echo $_SESSION['token'];
	if($_SESSION['token'] != null){
		return response()->view('welcome')
			->header('Authorization', 'Bearer ' . $_SESSION['token']);
	}
    return view('welcome');
});

Route::get('login', function(){
	return view('auth.login');
});

Route::post('login', function(Request $request) use ($dispatcher){
	$response = $dispatcher
		->with([
			'email' => $request->input('email'),
			'password' => $request->input('password')
		])
		->post('api/authenticate');
	$token = $response['token'];
	$_SESSION['token'] = $token;
	return redirect('/');
});

Route::get('register', function(){
	return view('auth.register');
});

Route::post('register', function(Request $request) use ($dispatcher){
	$dispatcher
		->with([
			'email' => $request->input('email'),
			'password' => $request->input('password'),
			'password_confirmation' => $request->input('password_confirmation')
		])
		->post('api/register');
	return redirect('/');
});
//Test routes
// Route::get('upload', function() {
//   return view('upload');
// });

// Route::post('upload', '\App\Http\Controllers\PictureController@store');

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

	//Farms
	$api->get('farms', 'App\Http\Controllers\FarmController@index');
	$api->get('farms/{id}', 'App\Http\Controllers\FarmController@show');

	//Pictures
	$api->get('pictures', 'App\Http\Controllers\PictureController@index');
	$api->get('pictures/{id}', 'App\Http\Controllers\PictureController@show');

	/////Updating and deleting data/////
	$api->group(['middleware' => 'api.auth'], function($api){

		//Geolocations
		$api->post('geolocations', 'App\Http\Controllers\GeolocationController@store');
		$api->put('geolocations/{id}', 'App\Http\Controllers\GeolocationController@update');
		$api->delete('geolocations/{id}', 'App\Http\Controllers\GeolocationController@destroy');
	
		//Farms
		$api->post('farms', 'App\Http\Controllers\FarmController@store');
		$api->put('farms/{id}', 'App\Http\Controllers\FarmController@update');
		$api->delete('farms/{id}', 'App\Http\Controllers\FarmController@destroy');
	
		//Pictures
		$api->post('pictures', 'App\Http\Controllers\PictureController@store');
		$api->put('pictures/{id}', 'App\Http\Controllers\PictureController@update');
		$api->delete('pictures/{id}', 'App\Http\Controllers\PictureController@delete');
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
