<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;
use App\Geolocation;

class APITest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->assertTrue(true);
    }

    //Geolocation testing
    public function testGeolocation(){
    	$this->createAuthenticatedUser();
        $geolocation = factory(Geolocation::class)->create();

        $this->callAuthenticated('GET', '/api/geolocations')->assertResponseOk();

        $this->callAuthenticated('GET', '/api/geolocations', [
        	'center' => ['lat' => $geolocation->latitude, 'long' => $geolocation->longitude],
        	'radius' => 20
        	])
        	->seeJson(['geolocationID' => $geolocation->geolocationID]);

        $this->callAuthenticated('POST', '/api/geolocations', [
        	'latitude' => 87,
        	'longitude' => 96
        	])
        	->assertResponseStatus(201);

        $this->callAuthenticated('GET', '/api/geolocations/' . $geolocation->geolocationID)
        	->assertResponseOk();

        $this->callAuthenticated('GET', '/api/geolocations/1000')
        	->assertResponseStatus(410);

        $this->callAuthenticated('PUT', '/api/geolocations/' . $geolocation->geolocationID, [
        	'lat' => 50
        	])
        	->assertResponseStatus(400);

        $this->callAuthenticated('PUT', '/api/geolocations/' . $geolocation->geolocationID, [
        	'latitude' => 50,
        	'longitude' => 50
        	])
        	->assertResponseStatus(204);

    	$this->callAuthenticated('DELETE', '/api/geolocations/' . $geolocation->geolocationID)
    		->assertResponseStatus(204);
    }
}
