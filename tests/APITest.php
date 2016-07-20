<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
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
        $geolocation = factory(Geolocation::class)->create();

    	$this->visit('/api/geolocations')
    		->assertResponseOk();

    	$this->get('/api/geolocations', [
    		'center' => ['lat' => $geolocation->latitude, 'long' => $geolocation->longitude],
    		'radius' => 20
    		])
    		->seeJson(['geolocationID' => $geolocation->geolocationID]);

    	$this->post('/api/geolocations', [
    		'latitude' => 87,
    		'longitude' => 96
    		])
    		->assertResponseStatus(201);

    	$this->get('/api/geolocations/' . $geolocation->geolocationID)
    		->assertResponseOk();

    	$this->get('/api/geolocations/1000')
    		->assertResponseStatus(410);

    	$this->put('/api/geolocations/' . $geolocation->geolocationID, [
    		'lat' => 50
    		])
    		->assertResponseStatus(400);

    	$this->put('/api/geolocations/' . $geolocation->geolocationID, [
    		'latitude' => 50,
    		'longitude' => 50,
    		])
    		->assertResponseStatus(204);
    }
}
