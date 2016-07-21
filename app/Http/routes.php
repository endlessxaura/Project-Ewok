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
	$api->post('register', 'App\Http\Controllers\AuthenticateController@register');

	$api->post('authenticate', 'App\Http\Controllers\AuthenticateController@authenticate');

	$api->group([], function($api){
		$api->resource('geolocations', 'App\Http\Controllers\GeolocationController');
	});
});

Route::auth();

Route::get('/home', 'HomeController@index');
