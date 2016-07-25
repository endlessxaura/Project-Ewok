<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Picture extends Model
{
    //DB connection
    protected $table = 'picture';
    protected $primaryKey = 'pictureID';
    public $timestamps = true;

    //Mass assignment
    protected $fillable = ['attachedModel', 'attachedID', 'filePath'];

    //Relationships
    public function getAttached(){
    	//PRE: This requires there to be an attached model
    	//		The attached model's primary key must be '[modelName]ID'
    	//		The primary key in the DB must also have [modelName]
    	//		It should always be lower case
    	//		These match the basic database nomenclature used
    	//		For example: farm = model name, farmID is primary key in DB
    	//POST: Returns the model the picture is attached to
    	//NOTE: you could rewrite this to have logic related to
    	//		attached model instead, if you wanted a different
    	//		DB structure. For example, I could do
    	//		if ($this->attachedModel == 'farm'){
    	//		return $this->belongsTo('App\Farm', 'attachedID', 'farmID')
    	//		}
    	//		for the same results
    	return $this->belongsTo(
    		'App\\' . $this->attachedModel,
    		'attachedID',
    		strtolower($this->attachedModel) . 'ID');
    }

    //Functions
}
