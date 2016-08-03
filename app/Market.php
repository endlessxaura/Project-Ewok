<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Market extends Model
{
    //DB connection
    protected $table = 'market';
    protected $primaryKey = 'marketID';
    public $timestamps = true;

    //Mass assignment
    protected $fillable = ['openingTime', 'closingTime', 'geolocationID'];

    //Relationships
    public function geolocation(){
    	return $this->morphMany('App\Geolocation', 'location');
    }

    public function pictures(){
        //NOTE: DO NOT USE THIS DIRECTLY; USE getPictures() FOR ACCURATE RESULTS
        return $this->hasMany('App\Picture', 'attachedID', 'farmID');
    }

    //Functions
    public function getPictures(){
        $possiblePictures = $this->pictures;
        $validPictures = [];
        foreach($possiblePictures as $possiblePicture){
            if($possiblePicture->attachedModel == 'market'){
                $validPictures[] = $possiblePicture;
            }
        }
        return $validPictures;
    }
}
