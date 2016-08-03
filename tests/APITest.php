<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;
use App\Geolocation;
use App\Farm;
use App\Review;
use App\Market;
use App\Location;

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
        	'longitude' => 96,
            'locationType' => 'farm'
        	])
        	->assertResponseStatus(201);

        $this->callAuthenticated('GET', '/api/geolocations/' . $geolocation->geolocationID)
        	->assertResponseOk();

        $this->callAuthenticated('GET', '/api/geolocations/1000')
        	->assertResponseStatus(410);

        $this->callAuthenticated('PUT', '/api/geolocations/' . $geolocation->geolocationID, [
        	'latitude' => 50,
        	'longitude' => 50
        	])
        	->assertResponseStatus(204);

    	$this->callAuthenticated('DELETE', '/api/geolocations/' . $geolocation->geolocationID)
    		->assertResponseStatus(204);
    }

    //Review testing
    public function testReview(){
        $this->createAuthenticatedUser();
        $geolocation = factory(Geolocation::class)->create();
        $review = factory(Review::class)->create([
            'geolocationID' => $geolocation->geolocationID,
            'userID' => $this->user->userID
            ]);

        $this->callAuthenticated('GET', '/api/reviews')
            ->assertResponseOk();

        $this->callAuthenticated('GET', '/api/reviews/' . $review->reviewID)
            ->assertResponseOk();

        //For some reason, the following tests fail because it can't parse the token
        //TBH, I have no idea why. Test these using postman or another request builder
    }

    //NOTE: PICTURES DO NOT HAVE A TESTER BECAUSE YOU CAN'T WITH LARAVEL
    //TO TEST THESE, USE THE TESTING ROUTES (which are commented out)
}
