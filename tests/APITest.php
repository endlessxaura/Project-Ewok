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
            'submitterLatitude' => 100,
            'submitterLongitude' => 100,
        	'latitude' => 87,
        	'longitude' => 96
        	])
        	->assertResponseStatus(400);

        $this->callAuthenticated('POST', '/api/geolocations', [
            'submitterLatitude' => 87,
            'submitterLongitude' => 96,
            'latitude' => 87,
            'longitude' => 96
            ])
            ->assertResponseStatus(201);

        $this->callAuthenticated('GET', '/api/geolocations/' . $geolocation->geolocationID)
        	->assertResponseOk();

        $this->callAuthenticated('GET', '/api/geolocations/1000')
        	->assertResponseStatus(410);

        $this->callAuthenticated('PUT', '/api/geolocations/' . $geolocation->geolocationID, [
        	'submitterLatitude' => $geolocation->latitude + .001,
            'submitterLongitude' => $geolocation->longitude + .001,
            'latitude' => $geolocation->latitude,
        	'longitude' => $geolocation->longitude
        	])
        	->assertResponseStatus(204);

    	// $this->callAuthenticated('DELETE', '/api/geolocations/' . $geolocation->geolocationID)
    	// 	->assertResponseStatus(204);

        $this->callAuthenticated('POST', '/api/geolocations/' . $geolocation->geolocationID . '/validate', [
            'submitterLatitude' => $geolocation->latitude + .001,
            'submitterLongitude' => $geolocation->longitude + .001,
            'valid' => 0
            ])
            ->assertResponseStatus(204);
    }

     //Farm testing
    public function testFarm(){
        $this->createAuthenticatedUser();
        $farm = factory(Farm::class)->create();

        $this->callAuthenticated('GET', '/api/farms')
            ->assertResponseOk();

        $this->callAuthenticated('GET', '/api/farms/' . $farm->farmID)
            ->assertResponseOk();

        $geolocation2 = factory(Geolocation::class)->create();
        $this->callAuthenticated('POST', '/api/farms', [
            'geolocationID' => $geolocation2->geolocationID,
            'openingTime' => null,
            'closingTime' => null
            ])
            ->assertResponseStatus(201);

        $this->callAuthenticated('PUT', '/api/farms/' . $farm->farmID, [
            'apples' => 1
            ])
            ->assertResponseStatus(204);

        $this->callAuthenticated('DELETE', '/api/farms/' . $farm->farmID)
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

        $this->callAuthenticated('GET', '/api/reviews', [
            'geolocationID' => $geolocation->geolocationID
            ])
            ->assertResponseOk();

        $this->callAuthenticated('GET', '/api/reviews/' . $review->reviewID)
            ->assertResponseOk();

        $geolocation2 = factory(Geolocation::class)->create();
        $this->callAuthenticated('POST', '/api/reviews', [
            'geolocationID' => $geolocation2->geolocationID,
            'comment' => 'fudge',
            'vote' => 1
            ])
            ->assertResponseStatus(201);

        $this->callAuthenticated('POST', '/api/reviews', [
            'geolocationID' => $geolocation->geolocationID,
            'comment' => 'Not gonna work'
            ])
            ->assertResponseStatus(409);

        $this->callAuthenticated('PUT', '/api/reviews/' . $review->reviewID, [
            'geolocationID' => $geolocation->geolocationID,
            'comment' => 'Fooeybar'
            ])
            ->assertResponseStatus(204);

        $this->callAuthenticated('PUT', '/api/reviews/' . $review->reviewID, [
            'geolocationID' => $geolocation2->geolocationID,
            'comment' => 'Fooeybar',
            'rating' => 50
            ])
            ->assertResponseStatus(400);

        $this->callAuthenticated('DELETE', '/api/reviews/' . $review->reviewID)
            ->assertResponseStatus(204);
    }

    //Market testing
    public function testMarket(){
        $this->createAuthenticatedUser();
        $market = factory(Market::class)->create();

        $this->callAuthenticated('GET', '/api/markets')
            ->assertResponseOk();

        $this->callAuthenticated('GET', '/api/markets/' . $market->marketID)
            ->assertResponseOk();

        $geolocation2 = factory(Geolocation::class)->create();
        $this->callAuthenticated('POST', '/api/markets', [
            'geolocationID' => $geolocation2->geolocationID,
            'openingTime' => null,
            'closingTime' => null
            ])
            ->assertResponseStatus(201);

        $this->callAuthenticated('PUT', '/api/markets/' . $market->marketID, [
            'apples' => 1
            ])
            ->assertResponseStatus(204);

        $this->callAuthenticated('DELETE', '/api/farms/' . $market->marketID)
            ->assertResponseStatus(204);
    }
    //NOTE: PICTURES DO NOT HAVE A TESTER BECAUSE YOU CAN'T WITH LARAVEL
    //TO TEST THESE, USE THE TESTING ROUTES (which are commented out)
}
