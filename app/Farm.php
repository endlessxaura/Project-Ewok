<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Farm extends Model
{
    //DB connection
    protected $table = 'farm';
    protected $primaryKey = 'farmID';
    public $timestamps = true;

    //Mass assignment
    protected $fillable = ['openingTime', 'closingTime', 'geolocationID'];

    //Relationships
    public function geolocation(){
    	return $this->hasOne('App\Geolocation', 'geolocationID', 'geolocationID');
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
            if($possiblePicture->attachedModel == 'farm'){
                $validPictures[] = $possiblePicture;
            }
        }
        return $validPictures;
    }
}
