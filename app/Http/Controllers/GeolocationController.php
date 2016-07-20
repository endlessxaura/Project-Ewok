<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Geolocation;
use App\Http\Requests;
use App\Http\Controllers\Responses;

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
        //      request MUST have the following
        //      center = the center of focus for looking as ['lat' => value, 'long' => value]
        //      radius = the distance you are looking
        //      unit (default = miles) = the unit you are looking for
        //          m = miles, n = nautical miles, k = kilometers
        //POST: returns all geopoints
        if($request->input('center') != null){
            $center = $request->input('center');
            $radius = $request->input('radius');
            $unit = $request->input('unit', 'm');
            return Geolocation::GetLocationsInRadius($radius, $center, $unit);
        }
        else{
            return Geolocation::all();
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
        //POST: Stores the specified geolocation in the DB
        $geolocation = new Geolocation;
        $geolocation->latitude = $request->input('latitude');
        $geolocation->longitude = $request->input('longitude');
        $geolocation->save();
        return Responses::Created();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //PRE: $id must match a geolocationID
        //POST: returns the geolocation matching the $id
        $geolocation = Geolocation::find($id);
        if($geolocation != null){
            return $geolocation;
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
