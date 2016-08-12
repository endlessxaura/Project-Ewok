<?php

namespace App;

use File;
use Response;
use Illuminate\Database\Eloquent\Model;

class Picture extends Model
{
   //DB connection
    protected $table = 'picture';
    protected $primaryKey = 'pictureID';
    public $timestamps = true;

    //Mass assignment
    protected $fillable = ['attached_id', 'attached_type', 'filePath'];

    //Relationships
    public function attached(){
        return $this->morphTo();
    }

    //Functions
    public function getImagePath(){
        return storage_path() . "/app/" . $this->filePath;
    }
}
