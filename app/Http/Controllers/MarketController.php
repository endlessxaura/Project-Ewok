<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Responses;
use App\Market;
use App\Geolocation;
use Schema;
use App\Http\Requests;

class MarketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //PRE: $request may include name to search by name
        //POST: Returns all markets, including their geolocation
        $markets = Market::all();
        foreach($markets as $market){
            $market->geolocation;
        }
        return $markets;
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
        //POST: stores the market in the database with the specified data
        $geolocation = Geolocation::find($request->input('geolocationID'));
        if($geolocation != null){
            if($geolocation->hasAttached() == false){
                //Creating new market
                $market = new Market;
                $market->geolocationID = $request->input('geolocationID');
                $geolocation->locationType = 'Market';
                $market->openingTime = $request->input('openingTime', null);
                $market->closingTime = $request->input('closingTime', null);

                //Updating each crop
                $columns = Schema::getColumnListing('market');
                for($i = 7; $i < sizeof($columns); $i++){
                    $market[$columns[$i]] = $request->input($columns[$i], 0);
                }

                //Saving
                $market->save();
                $geolocation->save();
                return Responses::Created($market->marketID);
            }
            else{
                return Responses::AlreadyExists();
            }
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
        //PRE: $id must match a market's marketID
        //POST: returns the specified market from the DB with geolocation
        $market = Market::find($id);
        if($market != null){
            $market->geolocation;
            return $market;
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
        //PRE: $id must match a market's ID
        //      request may contain the following
        //      openingTime
        //      closingTime
        //POST: updates the market with the specified information
        //NOTE: GEOLOCATION CANNOT BE CHANGED
        $market = Market::find($id);
        if($market != null){
            $market->openingTime = $request->input('openingTime', $market->openingTime);
            $market->closingTime = $request->input('closingTime', $market->closingTime);
            
            //Updating each crop
            $columns = Schema::getColumnListing('market');
            for($i = 7; $i < sizeof($columns); $i++){
                $market[$columns[$i]] = $request->input($columns[$i], 0);
            }

            //Saving
            $market->save();
            return Responses::Updated();
        }
        else{
            return Responses::DoesNotExist('Market');
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
        //PRE: $id must match a market's ID
        //POST: deletes the market from the database
        $market = Market::find($id);
        if($market != null){
            $market->delete();
            return Responses::Updated();
        }
        else{
            return Responses::DoesNotExist();
        }
    }
}
