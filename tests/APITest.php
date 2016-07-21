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
    	$user = factory(User::class)->create();
        $geolocation = factory(Geolocation::class)->create();

    	$this//->actingAs($user)
    		->get('/api/geolocations')
    		->assertResponseOk();

    	$this//->actingAs($user)
    		->get('/api/geolocations', [
    		'center' => ['lat' => $geolocation->latitude, 'long' => $geolocation->longitude],
    		'radius' => 20
    		])
    		->seeJson(['geolocationID' => $geolocation->geolocationID]);

    	$this//->actingAs($user)
    		->post('/api/geolocations', [
    		'latitude' => 87,
    		'longitude' => 96
    		])
    		->assertResponseStatus(201);

    	$this//->actingAs($user)
    		->get('/api/geolocations/' . $geolocation->geolocationID)
    		->assertResponseOk();

    	$this//->actingAs($user)
    		->get('/api/geolocations/1000')
    		->assertResponseStatus(410);

    	$this//->actingAs($user)
    		->put('/api/geolocations/' . $geolocation->geolocationID, [
    		'lat' => 50
    		])
    		->assertResponseStatus(400);

    	$this//->actingAs($user)
    		->put('/api/geolocations/' . $geolocation->geolocationID, [
    		'latitude' => 50,
    		'longitude' => 50,
    		])
    		->assertResponseStatus(204);
    }
}
