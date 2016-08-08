<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Geolocation extends Model
{
    //DB connection
    protected $table = 'geolocation';
    protected $primaryKey = 'geolocationID';
    public $timestamps = true;

    //Mass assignment
    protected $fillable = ['geolocationID', 'latitude', 'longitude', 'location_type', 'location_id'];

    //Relationships
    public function reviews(){
        return $this->hasMany('App\Review', 'geolocationID', 'geolocationID');
    }

    public function users(){
        return $this->belongsToMany('App\User', 'submission', 'geolocationID', 'userID')
            ->withPivot('compassDirection', 'valid', 'latitude', 'longitude')
            ->withTimestamps();
    }

    public function location(){
        return $this->morphTo();
    }
    
    public function pictures(){
        return $this->morphMany('App\Picture', 'attached');
    }
    //Functions
    public function hasAttached(){
        //POST: returns true if the geolocation has something attached, false otherwise
        //NOTE: this requires that any model that can be attached to it change the geolocations
        //      locationType to match the name of the MODEL (not the table) that it is attache to
        if($this->location != null){
            return true;
        }
        else{
            return false;
        }
    }

    public static function GetLocationsInRadius($distance, $center, $unit){
    	//PRE: distance is a number; $unit is k for kilometers, n for nautical miles, m for miles
    	//		center is the coordinates for the center as ['lat' => value, 'long' => value]
    	//POST: returns all the points in the radius
    	if($unit == 'k'){
    		$R = 6371;
    	}
    	else if($unit == 'm'){
    		$R = 3959;
    	}
    	else if($unit == 'n'){
    		$R = 3440;
    	}

    	$lat = $center['lat'];
    	$lon = $center['long'];
    	$rad = $distance;

	    //first-cut bounding box (in degrees)
	    $maxLat = $lat + rad2deg($rad/$R);
	    $minLat = $lat - rad2deg($rad/$R);
	    $maxLon = $lon + rad2deg(asin($rad/$R) / cos(deg2rad($lat)));
	    $minLon = $lon - rad2deg(asin($rad/$R) / cos(deg2rad($lat)));

	    $locations = Geolocation::whereBetween('latitude', [$minLat, $maxLat])
	    	->whereBetween('longitude', [$minLon, $maxLon])
	    	->get();

	    $validLocations = array();

	    foreach($locations as $location){
	    	if(Geolocation::distance($location->latitude, $location->longitude, $lat, $lon, $unit) < $distance){
	    		$validLocations[] = $location;
	    	}
	    }

	    return $validLocations;
    }

    public static function distance($lat1, $lon1, $lat2, $lon2, $unit) {
    	//PRE: $unit is k for kilometers, n for nautical miles, m for miles
    	//POST: returns the distance between the two points
	    $theta = $lon1 - $lon2;
	    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
	    $dist = acos($dist);
	    $dist = rad2deg($dist);
	    $miles = $dist * 60 * 1.1515;
	    $unit = strtoupper($unit);

	    if ($unit == "K") {
	        return ($miles * 1.609344);
	    } else if ($unit == "N") {
	        return ($miles * 0.8684);
	    } else {
	        return $miles;
	    }
	}

    public function information(){
        //POST: Returns information based on the type of location it is
        //      location types allowed:
        //      farm
        //NOTE: This part is not modular. Remove this when exporting to a new project
        $information = array();
        $information['name'] = $this->name;
        $information['description'] = $this->description;
        $information['locationInfo'] = $this->location;
        $information['images'] = $this->pictures;
        $information['geolocationID'] = $this->geolocationID;
        return $information;
    }

    public function testValidity(){
        //POST: tests the validity of the geolocation and deletes it if it is not valid
        $validations = array();
        foreach ($this->users as $user){
            $validations[] = $user->pivot->valid;
        }
        $validity = 0;
        foreach($validations as $validation){
            $validity += $validation;
        }
        if($validity < count($validations) * .5){        
            $this->users()->sync([]);
            $reviews = $this->reviews;
            foreach($reviews as &$review){
                $review->delete();
            }
            $this->location->delete();
            $this->delete();
        }
    }
}
