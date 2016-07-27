<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Storage;
use File;
use App\Http\Requests;
use App\Http\Controllers\Responses;
use App\Picture;

class PictureController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //PRE: $request may have attachedID and attachedModel
        //POST: returns the specified picture or all pictures
        if($request->has('attachedID') && $request->has('attachedModel')){
            return Picture::where('attachedModel', '=', $request->input('attachedModel'))
                ->where('attachedID', '=', $request->input('attachedID'))
                ->get();
        }
        else{
            return Picture::all();
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //PRE: $request must contains the following
        //          image = the file being uploaded
        //          attachedItem = the thing the picture is being attached to (eg. farm)
        //              available attachedItems: farm
        //          attachedID = the ID of the attached object
        //      the attached item must also have a name
        //POST: stores the picture in the DB and the system
        $validator = Validator::make($request->all(), [
            'image' => 'required|file',
            'attachedItem' => 'required|alpha',
            'attachedID' => 'required|integer'
            ]);
        if((!$validator->fails()) && $request->hasFile('image') && $request->file('image')->isValid()){
            //Creating the model
            $picture = Picture::create([
                'attachedModel' => $request->input('attachedItem'),
                'attachedID' => $request->input('attachedID')
                ]);

            //Naming the file
            $file = $request->file('image');
            $attachedItem = $picture->getAttached;
            $fileName = $request->input('attachedItem') . '/' . $attachedItem->name . '/' . $file->getClientOriginalName();

            //Storing the image
            $file = $request->file('image');
            Storage::put(
                $fileName,
                File::get($file)
                );

            //Updating model with filepath
            $picture->filePath = "../storage/app/" . $fileName;
            $picture->save();

            //Returning response
            return Responses::Created();
        }
        else{
            return Responses::BadRequest();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $picture = Picture::find($id);
        if($picture != null){
            return $picture;
        }
        else{
            return Responses::DoesNotExist();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //PRE: $request may have the following
        //      attachedItem
        //      attachedID
        //      filePath
        //      image
        //POST: Updates the picture with the specified information
        $picture = Picture::find($id);
        if($picture != null){            
            //Checking for image
            $file = $request->file('image', null);
            if($file != null){   
                //Updating information
                $picture->attachedItem = $request->input('attachedItem', $picture->attachedItem);
                $picture->attachedID = $request->input('attachedID', $picture->attachedID);
                $attachedItem = $picture->getAttached();
                $fileName = $picture->attachedItem . '/' . $attachedItem->name . '/' . $file->getClientOriginalName();
                $picture->filePath = "../storage/app/" . $fileName;
                
                //Storing new image         
                Storage::put(
                $fileName,
                File::get($file)
                );

                //Deleting old image
                $filePath = $picture->filePath;
                $directoryStructure = explode('/', $filePath);
                $deletedFile = $directoryStructure[3] . '/' . $directoryStructure[4] . '/' . $directoryStructure[5];
                Storage::delete($deletedFile);            

                //Saving and returning
                $picture->save();
                return Responses::Updated();
            }
            else{
                return Responses::BadRequest();
            }

        }
        else{
            return Responses::DoesNotExist();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //POST: Deletes the picture matching ID
        $picture = Picture::find($id);
        if($picture != null){
            $picture->delete();
            return Responses::Updated();
        }
        else{
            return Responses::DoesNotExist();
        }
    }
}
