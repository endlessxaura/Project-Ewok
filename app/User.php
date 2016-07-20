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
    protected $fillable = ['email','password', 'api_token'];

    //Relationships

}
