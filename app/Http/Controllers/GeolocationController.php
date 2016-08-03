<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Geolocation;
use App\Http\Requests;
use App\Http\Controllers\Responses;
use Illuminate\Http\Response;
use Auth;

class GeolocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //PRE: The request 'radius' input is optional, but if it is there
        //      request MUST have the following
        //      latitude = the latitude of the center
        //      longitude = the longitude of the center
        //      radius = the distance you are looking
        //      unit (default = miles) = the unit you are looking for
        //          m = miles, n = nautical miles, k = kilometers

        //      the request may have the following      
        //      locationType (farm, market, etc)
        //      name (filters for the name)
        //      operatingTime (check if its open during this time; format is 00:00:00)

        //      You may also specify, in the URL, GeoJSON = 0 to make it return just the object

        
        //POST: returns all geopoints, in a radius is specified, as a GeoJson
        //NOTE: This can take a long time, especially if you aren't using a center
        //      Make sure to load this asynchronously to avoid significant lag
        //      Also, try to use a radius at all times
        
        //Flags
        $radiusFlag = false;
        $typeFlag = false;
        $nameFlag = false;
        $timeFlag = false;
        if($request->input('radius') != null){
            $latitude = $request->input('latitude');
            $longitude = $request->input('longitude');
            $radius = $request->input('radius');
            $unit = $request->input('unit', 'm');
            $radiusFlag = true;
        }
        // if($request->input('locationType') != null){
        //     $locationType = $request->input('locationType');
        //     $typeFlag = true;
        // }
        if($request->input('name') != null){
            $name = $request->input('name');
            $nameFlag = true;
        }
        if($request->input('operatingTime')){
            $operatingTime = $request->input('operatingTime');
            $timeFlag = true;
        }

        //Fetching locations
        if($radiusFlag){
            $geolocations = Geolocation::GetLocationsInRadius(
                $radius, 
                ['lat' => $latitude, 'long' => $longitude],
                $unit
                );
            if($typeFlag){
                foreach($geolocations as $geolocation){
                    if($geolocation->locationType != $locationType){
                        unset($geolocations[array_search($geolocation, $geolocations)]);
                    }
                }
            }
        }
        // else if($typeFlag){
        //     $geolocations = Geolocation::where('locationType', '=', $locationType)
        //         ->get()
        //         ->all();
        // }
        else{
            $geolocations = Geolocation::all()->all();
        }        

        //Filtering options
        if($nameFlag){
            foreach($geolocations as $geolocation){
                $information = $geolocation->information();
                if(!isset($information['name'])){
                    unset($geolocations[array_search($geolocation, $geolocations)]);
                }
                else if(strpos($information['name'], $name) === false){
                    unset($geolocations[array_search($geolocation, $geolocations)]);
                }
            }
        }
        if($timeFlag){
            $timeArray = explode( ":", $operatingTime);
            $operatingTime = 0;
            for($i = 0; $i < count($timeArray); $i++){
                $operatingTime += $timeArray[$i] * (pow(10, $i * -2));
            }
            foreach($geolocations as $geolocation){
                $information = $geolocation->information();
                if(!isset($information['openingTime']) && !isset($information['closingTime'])){
                    unset($geolocations[array_search($geolocation, $geolocations)]);
                }
                else{
                    $openingTime = explode(":", $information['openingTime']);
                    $openingTime = $openingTime[0] + ($openingTime[1] * .01) + ($openingTime[2] * .0001);
                    $closingTime = explode(":", $information['closingTime']);
                    $closingTime = $closingTime[0] + ($closingTime[1] * .01) + ($closingTime[2] * .0001);
                    $flag = true;       //True if we want to get rid of it
                    if($closingTime < $openingTime){
                        if($operatingTime >= $openingTime || $operatingTime < $closingTime){
                            $flag = false;
                        }
                    }
                    else{
                        if($operatingTime >= $openingTime && $operatingTime < $closingTime){
                            $flag = false;
                        }
                    }
                    if($flag){
                        unset($geolocations[array_search($geolocation, $geolocations)]);
                    }
                }
            }
        }

        //Formatting output
        if($request->input('GeoJSON', 1)){
            $features = array();
            foreach($geolocations as $geolocation){
                $features[] = [
                    "type" => "Feature",
                    "geometry" => ["type" => "Point", "coordinates" => [
                            $geolocation->longitude, 
                            $geolocation->latitude
                            ]
                        ],
                    "properties" => $geolocation->information()
                ];
            }
            return response()->json([
                "type" => "FeatureCollection",
                "features" => $features
            ]);
        }
        else{
            foreach($geolocations as &$geolocation){
                $geolocation->information();
            }
            return $geolocations;
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //PRE: request should contain the following
        //      latitude
        //      longitude
        //      locationType
        //      name
        //      description
        //      compassDirection
        //POST: Stores the specified geolocation in the DB
        $geolocation = new Geolocation;
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        if($latitude == null || $longitude == null){
            return Responses::BadRequest();
        }
        $geolocation->latitude = $latitude;
        $geolocation->longitude = $longitude;
        $geolocation->locationType = $request->input('locationType');
        $geolocation->name = $request->input('name');
        $geolocation->description = $request->input('description');
        $geolocation->save();
        $geolocation->users()->attach(Auth::user()->userID, [
            'compassDirection' => $request->input('compassDirection'),
            'valid' => 1
            ]);
        return Responses::Created($geolocation->geolocationID);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        //PRE: $id must match a geolocationID
        //      request may also specify GeoJSON = 0 to return just the object
        //POST: returns the geolocation matching the $id as a GeoJson
        $geolocation = Geolocation::find($id);
        if($geolocation != null){
            if($request->input('GeoJSON', 1)){
                return response()->json([
                "type" => "Feature",
                "geometry" => [
                    "type" => "Point",
                    "coordinates" => [
                        $geolocation->longitude,
                        $geolocation->latitude
                        ]
                    ],
                "properties" => $geolocation->information()
                ]);
            }
            else{
                foreach($geolocations as $geolocation){
                    $geolocation->information();
                }
                return $geolocation;
            }
        }
        else{
            return Responses::DoesNotExist('Geolocation');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //PRE: request may contain the following
        //      latitude
        //      longitude
        //      locationType
        //      name
        //      description
        //      compassDirection
        //      $id must match a geolocationID
        //POST: overwrites the previous data with the new values
        $geolocation = Geolocation::find($id);
        if($geolocation != null){
            $geolocation->latitude = $request->input('latitude', $geolocation->latitude);
            $geolocation->longitude = $request->input('longitude', $geolocation->longitude);
            $geolocation->locationType = $request->input('locationType', $geolocation->locationType);
            $geolocation->name = $request->input('name', $geolocation->name);
            $geolocation->description = $request->input('description', $geolocation->description);
            $geolocation->save();
            if(!($geolocation->users->contains(Auth::user()->userID))){    
                $geolocation->users()->attach(Auth::user()->userID, [
                    'compassDirection' => $request->input('compassDirection'),
                    'valid' => 1
                ]);              
            }
            else{
                $geolocation->users()->updateExistingPivot(Auth::user()->userID, [
                    'compassDirection' => $request->input('compassDirection', $geolocation->users->get(Auth::user()->userID)->pivot->compassDirection),
                    'valid' => 1
                ]);
            }
            return Responses::Updated();
        }
        else{
            return Responses::DoesNotExist('Geolocation');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
        //POST: destroys the geolocation
    {
        $geolocation = Geolocation::find($id);
        if($geolocation != null){
            $geolocation->users()->sync([]);
            $reviews = $geolocation->reviews;
            foreach($reviews as &$review){
                $review->delete();
            }
            $geolocation->delete();
            return Responses::Updated();
        }
        else{
            return Responses::DoesNotExist('Geolocation');
        }
    }

    public function validation(Request $request, $id){
        //PRE: request must contain valid as a bool
        //      request must contain compassDirection
        //POST: creates a submission about whether or not it is balid
        $geolocation = Geolocation::find($id);
        if($request->input('valid') == null || $request->input('compassDirection') == null){
            return Responses::BadRequest();
        }
        if($geolocation != null){
            if(!($geolocation->users->contains(Auth::user()->userID))){    //This breaks phpunit?
                $geolocation->users()->attach(Auth::user()->userID, [
                    'compassDirection' => $request->input('compassDirection'),
                    'valid' => $request->input('valid')
                ]);              
            }
            else{
                $geolocation->users()->updateExistingPivot(Auth::user()->userID, [
                    'compassDirection' => $request->input('compassDirection'),
                    'valid' => $request->input('valid')
                ]);
            }
            return Responses::Updated();
        }
        else{
            return Responses::DoesNotExist('Geolocation');
        }
    }
}
