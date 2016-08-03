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
}
