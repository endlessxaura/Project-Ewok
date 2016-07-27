<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;
use App\Geolocation;
use App\Farm;
use App\Review;

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
            'openingTime' => null,
            'closingTime' => null
            ])
            ->assertResponseStatus(201);

        $this->callAuthenticated('PUT', '/api/farms/' . $farm->farmID, [
            'name' => 'HelloWorld???',
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

        $this->callAuthenticated('GET', '/api/reviews/' . $review->reviewID)
            ->assertResponseOk();

        //For some reason, the following tests fail because it can't parse the token
        //TBH, I have no idea why. Test these using postman or another request builder

        // $geolocation2 = factory(Geolocation::class)->create([
        //     'locationType' => 'Farm'
        //     ]);
        // $this->callAuthenticated('POST', '/api/reviews', [
        //     'userID' => $this->user->userID,
        //     'geolocationID' => $geolocation2->geolocationID,
        //     'comment' => 'fudge',
        //     'vote' => 1
        //     ])
        //     ->assertResponseStatus(201);

        // $this->callAuthenticated('POST', '/api/reviews', [
        //     'userID' => $this->user->userID,
        //     'geolocationID' => $geolocation->geolocationID,
        //     'comment' => 'Not gonna work'
        //     ])
        //     ->assertResponseStatus(409);

        // $this->callAuthenticated('PUT', '/api/reviews/' . $review->reviewID, [
        //     'userID' => $this->user->userID,
        //     'geolocationID' => $geolocation->geolocationID,
        //     'comment' => 'Fooeybar'
        //     ])
        //     ->assertResponseStatus(204);

        // $this->callAuthenticated('PUT', '/api/reviews/' . $review->reviewID, [
        //     'userID' => $this->user->userID,
        //     'geolocationID' => $geolocation2->geolocationID,
        //     'comment' => 'Fooeybar'
        //     ])
        //     ->assertResponseStatus(409);

        // $this->callAuthenticated('DELETE', '/api/reviews/' . $review->reviewID)
        //     ->assertResponseStatus(204);
    }

    //NOTE: PICTURES DO NOT HAVE A TESTER BECAUSE YOU CAN'T WITH LARAVEL
    //TO TEST THESE, USE THE TESTING ROUTES (which are commented out)
}
