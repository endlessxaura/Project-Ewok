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
    protected $fillable = ['name', 'timeOfOperation', 'geolocationID'];

    //Relationships
    public function geolocation(){
    	return $this->hasOne('App\Geolocation', 'geolocationID', 'geolocationID');
    }

    //Functions
}
