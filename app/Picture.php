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
    protected $fillable = ['attached_id', 'attached_type', 'filePath'];

    //Relationships
    public function attached(){
        return $this->morphTo();
    }
}
