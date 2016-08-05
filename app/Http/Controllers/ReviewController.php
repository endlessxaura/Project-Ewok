<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Review;
use App\Http\Controllers\Responses;
use App\Http\Requests;
use Auth;
use App\Geolocation;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //PRE: $request may contain the geolocationID to pull up a list of all reviews related to that geolocationID
        //      $request may contain a userID to pull a list of all reviews that user made
        //      These may be combined, as well
        //POST: returns a list of reviews, drilling down by geolocation and user if provided
        if($request->input('geolocationID', null)){
            if($request->input('userID', null)){
                return Review::where('geolocationID', '=', $request->input('geolocationID'))
                    ->where('userID', '=', $request->input('userID'))
                    ->get();
            }
            return Review::where('geolocationID', '=', $request->input('geolocationID'))
                ->get();
        }
        if($request->input('userID', null)){
            return Review::where('userID', '=', $request->input('userID'))
                ->get();
        }
        return Review::all();
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
        //PRE: $request MUST contain a comment, userID, and geolocationID
        //     $request may contain rating (0 to 5)
        //      The user cannot have a review for that geolocation already
        //POST: stores the specified review in the database
        $userID = Auth::user()->userID;
        $previousReview = Review::where('userID', '=', $userID)
            ->where('geolocationID', '=', $request->input('geolocationID'))
            ->get();
        if($previousReview->isEmpty()){
            if(Geolocation::find($request->input('geolocationID')) != null){
                if($request->input('rating') <= 5 && $request->input('rating') >= 0){
                    $review = new Review;
                    $review->userID = $userID;
                    $review->geolocationID = $request->input('geolocationID');
                    $review->comment = $request->input('comment');
                    $review->rating = $request->input('rating', 0);
                    $review->save();
                    return Responses::Created($review->reviewID);  
                }
                else{
                    return Responses::BadRequest();
                }
            }
            else{
                return Responses::DoesNotExist('Geolocation');
            }
        }
        else{
            return Responses::AlreadyExists();
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
        //POST: returns the view with a matching ID
        $review = Review::find($id);
        if($review != null){
            $review->pictures;
            return $review;
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
        //PRE: $request may contain a comment, userID, and geolocationID
        //     $request may contain rating (0 to 5)
        //      The user MUST own the review
        //POST: stores the specified review in the database
        $review = Review::find($id);
        $userID = Auth::user()->userID;
        $previousReview = null;
        if($review != null){
            if($review->userID == $userID){
                if($request->input('rating', $review->rating) <= 5 && $request->input('rating', $review->rating) >= 0){
                    $review->geolocationID = $request->input('geolocationID');
                    $review->comment = $request->input('comment', $review->comment);
                    $review->rating = $request->input('rating', $review->rating);
                    $review->save();
                    return Responses::Updated();
                }
                else{
                    return Responses::BadRequest();
                }
            }
            else{
                return Responses::PermissionDenied();
            }
        }
        else{
            return Responses::DoesNotExist('Review');
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
        //PRE: The user MUST own the review
        //POST: deletes the review with a matching ID
        $review = Review::find($id);
        $userID = Auth::user()->userID;
        if($review != null){           
            if($review->userID == $userID){
                $review->delete();
                return Responses::Updated();
            }
            else{
                return Responses::PermissionDenied();
            }
        }
        else{
            return Responses::DoesNotExist('Review');
        }
    }
}
