<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;
use App\Geolocation;
use App\Farm;

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
        	->assertResponseOk();

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

    //Farm testing
    public function testFarm(){
        $this->createAuthenticatedUser();
        $geolocation = factory(Geolocation::class)->create([
            'locationType' => 'Farm'
            ]);
        $farm = factory(Farm::class)->create([
            'geolocationID' => $geolocation->geolocationID
            ]);

        $this->callAuthenticated('GET', '/api/farms')
            ->assertResponseOk();

        $this->callAuthenticated('GET', '/api/farms/' . $farm->farmID)
            ->assertResponseOk();

        $geolocation2 = factory(Geolocation::class)->create([
            'locationType' => 'Farm'
            ]);
        $this->callAuthenticated('POST', '/api/farms', [
            'geolocationID' => $geolocation2->geolocationID,
            'name' => 'HelloWorld',
            'timeOfOperation' => null
            ])
            ->assertResponseStatus(201);

        $this->callAuthenticated('PUT', '/api/farms/' . $farm->farmID, [
            'name' => 'HelloWorld???'
            ])
            ->assertResponseStatus(204);

        $this->callAuthenticated('DELETE', '/api/farms/' . $farm->farmID)
            ->assertResponseStatus(204);
    }
}
