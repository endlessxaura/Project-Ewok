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
        if($request->has('name')){
            $farms = Farm::where('name', '=', $request->input('name'))->get();
        }
        else{
            $farms = Farm::all();
        }
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
        //      name
        //      timeOfOperation
        //      A lot of different crops and livestock as booleans (1 for available) (see DB for all of them)
        //POST: stores the farm in the database with the specified data
        $geolocation = Geolocation::find($request->input('geolocationID'));
        if($geolocation != null){
            $farm = new Farm;
            $farm->geolocationID = $request->input('geolocationID');
            $farm->name = $request->input('name');
            $farm->openingTime = $request->input('openingTime', null);
            $farm->closingTime = $request->input('closingTime', null);

            //Updating each crop
            $columns = Schema::getColumnListing('farm');
            for($i = 7; $i < sizeof($columns); $i++){
                $farm[$columns[$i]] = $request->input($columns[$i], 0);
            }
            // $farm->apples = $request->input('apples', 0);
            // $farm->corn = $request->input('corn', 0);
            // $farm->wheat = $request->input('wheat', 0);
            // $farm->lettuce = $request->input('lettuce', 0);
            // $farm->chickens = $request->input('chickens', 0);
            // $farm->cows = $request->input('cows', 0);
            // $farm->pigs = $request->input('pigs', 0);
            // $farm->tomatoes = $request->input('tomatoes', 0);
            // $farm->soybean = $request->input('soybean', 0);
            // $farm->potatoes = $request->input('potatoes', 0);
            // $farm->grapes = $request->input('grapes', 0);
            // $farm->bananas = $request->input('bananas', 0);
            // $farm->

            //Saving
            $farm->save();
            return Responses::Created();
        }
        else{
            return Responses::BadRequest();
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
        //      geolocationID
        //      name
        //      timeOfOperation
        //POST: updates the farm with the specified information
        $farm = Farm::find($id);
        if($farm != null){
            $farm->geolocationID = $request->input('geolocationID', $farm->geolocationID);
            $farm->name = $request->input('name', $farm->name);
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
            return Responses::DoesNotExist();
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
