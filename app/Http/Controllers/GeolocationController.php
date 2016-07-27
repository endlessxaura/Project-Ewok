<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Geolocation;
use App\Http\Requests;
use App\Http\Controllers\Responses;
use Illuminate\Http\Response;

class GeolocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //PRE: The request 'center' input is optional, but if it is there!
        //      You may also specify, in the URL, GeoJSON = 0 to make it return just the object
        //      request MUST have the following
        //      latitude = the latitude of the center
        //      longitude = the longitude of the center
        //      radius = the distance you are looking
        //      unit (default = miles) = the unit you are looking for
        //          m = miles, n = nautical miles, k = kilometers
        //POST: returns all geopoints, in a radius is specified, as a GeoJson
        //NOTE: This can take a long time, especially if you aren't using a center
        //      Make sure to load this asynchronously to avoid significant lag
        if($request->input('radius') != null){
            $latitude = $request->input('latitude');
            $longitude = $request->input('longitude');
            $radius = $request->input('radius');
            $unit = $request->input('unit', 'm');
            $geolocations = Geolocation::GetLocationsInRadius($radius, ['lat' => $latitude, 'long' => $longitude], $unit);            
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
        else{
            $geolocations = Geolocation::all();
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
        //POST: Stores the specified geolocation in the DB
        $geolocation = new Geolocation;
        $geolocation->latitude = $request->input('latitude');
        $geolocation->longitude = $request->input('longitude');
        $geolocation->locationType = $request->input('locationType');
        $geolocation->save();
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
        //PRE: request must contain latitude and longitude
        //      $id must match a geolocationID
        //POST: overwrites the previous latitude and longitude with the new values
        $lat = $request->input('latitude');
        $long = $request->input('longitude');
        if($lat != null && $long != null){
            $geolocation = Geolocation::find($id);
            if($geolocation != null){
                $geolocation->latitude = $lat;
                $geolocation->longitude = $long;
                $geolocation->locationType = $request->input('locationType', $geolocation->locationType);
                $geolocation->save();
                return Responses::Updated();
            }
            else{
                return Responses::DoesNotExist('Geolocation');
            }
        }
        else{
            return Responses::BadRequest();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $geolocation = Geolocation::find($id);
        if($geolocation != null){
            $geolocation->delete();
            return Responses::Updated();
        }
        else{
            return Responses::DoesNotExist('Geolocation');
        }
    }
}
