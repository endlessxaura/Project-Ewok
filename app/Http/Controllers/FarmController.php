<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Farm;
use App\Geolocation;
use App\Http\Requests;
use App\Http\Controllers\Responses;
use Illuminate\Http\Response;
use Schema;

class FarmController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //POST: Returns all farms, including their geolocation
        $farms = Farm::all();
        foreach($farms as $farm){
            $farm->geolocation;
        }
        return $farms;
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
        //PRE: request must contain the following
        //      geolocationID
        //      openingTime
        //      closingTime
        //      A lot of different crops and livestock as booleans (1 for available) (see DB for all of them)
        //POST: stores the farm in the database with the specified data
        $geolocation = Geolocation::find($request->input('geolocationID'));
        if($geolocation != null){
            if($geolocation->hasAttached() == false){
                //Creating new farm
                $farm = new Farm;
                $geolocation->location_type = 'App\Farm';
                $farm->openingTime = $request->input('openingTime', null);
                $farm->closingTime = $request->input('closingTime', null);

                //Updating each crop
                $columns = Schema::getColumnListing('farm');
                for($i = 7; $i < sizeof($columns); $i++){
                    $farm[$columns[$i]] = $request->input($columns[$i], 0);
                }

                //Saving
                $farm->save();
                $geolocation->location_id = $farm->farmID;
                $geolocation->save();
                return Responses::Created($farm->farmID);
            }
            else{
                return Responses::AlreadyExists();
            }
        }
        else{
            return Responses::DoesNotExist('Geolocation');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //PRE: $id must match a farm's farmID
        //POST: returns the specified farm from the DB with geolocation
        $farm = Farm::find($id);
        if($farm != null){
            $farm->geolocation;
            $farm->pictures;
            return $farm;
        }
        else{
            return Responses::DoesNotExist();
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
        //PRE: $id must match a farm's ID
        //      request may contain the following
        //      openingTime
        //      closingTime
        //POST: updates the farm with the specified information
        //NOTE: GEOLOCATION CANNOT BE CHANGED
        $farm = Farm::find($id);
        if($farm != null){
            $farm->openingTime = $request->input('openingTime', $farm->openingTime);
            $farm->closingTime = $request->input('closingTime', $farm->closingTime);
            
            //Updating each crop
            $columns = Schema::getColumnListing('farm');
            for($i = 7; $i < sizeof($columns); $i++){
                $farm[$columns[$i]] = $request->input($columns[$i], 0);
            }

            //Saving
            $farm->save();
            return Responses::Updated();
        }
        else{
            return Responses::DoesNotExist('Farm');
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
        //PRE: $id must match a farm's ID
        //POST: deletes the farm from the database
        $farm = Farm::find($id);
        if($farm != null){
            $farm->delete();
            return Responses::Updated();
        }
        else{
            return Responses::DoesNotExist();
        }
    }
}
