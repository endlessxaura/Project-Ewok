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
    protected $fillable = ['userID', 'comment', 'rating', 'geolocationID'];

    //Relationships
    public function user(){
    	return $this->belongsTo('App\User', 'userID', 'userID');
    }

    public function geolocation(){
    	return $this->belongsTo('App\Geolocation', 'geolocationID', 'geolocationID');
    }

    public function pictures(){
        return $this->morphMany('App\Picture', 'attached');
    }
}
