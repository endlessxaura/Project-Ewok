<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Geolocation extends Model
{
    //DB connection
    protected $table = 'geolocation';
    protected $primaryKey = 'geolocationID';
    protected $timestamps = true;

    //Relationships
}
