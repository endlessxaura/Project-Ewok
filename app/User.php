<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    //DB connection
    protected $table = 'user';
    protected $primaryKey = 'userID';
    public $timestamps = true;

    //Mass assignment
    protected $fillable = ['firstName', 'lastName', 'email','password'];

    //Relationships
    public function reviews(){
    	return $this->hasMany('App\Review', 'userID', 'userID');
    }

    public function geolocations(){
        return $this->belongsToMany('App\Geolocation', 'submission', 'userID', 'geolocationID')
            ->withPivot('compassDirection', 'valid', 'latitude', 'longitude')
            ->withTimestamps();
    }

    public function pictures(){
        return $this->morphMany('App\Picture', 'attached');
    }

    //Functions
    
}
