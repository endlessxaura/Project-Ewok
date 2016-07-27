<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
 	//DB connection
    protected $table = 'review';
    protected $primaryKey = 'reviewID';
    public $timestamps = true;

    //Mass assignment
    protected $fillable = ['userID', 'comment', 'vote', 'geolocationID'];

    //Relationships
    public function user(){
    	return $this->belongsTo('App\User', 'userID', 'userID');
    }

    public function geolocation(){
    	return $this->belongsTo('App\Geolocation', 'geolocationID', 'geolocationID');
    }

    public function pictures(){
        //NOTE: DO NOT USE THIS DIRECTLY; USE getPictures() FOR ACCURATE RESULTS
        return $this->hasMany('App\Picture', 'attachedID', 'reviewID');
    }

    //Functions
    public function getPictures(){
        $possiblePictures = $this->pictures;
        $validPictures = [];
        foreach($possiblePictures as $possiblePicture){
            if($possiblePicture->attachedModel == 'review'){
                $validPictures[] = $possiblePicture;
            }
        }
        return $validPictures;
    }
}