<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Picture;
use App\Http\Requests;
use App\Http\Controllers\Responses;
use Illuminate\Http\Response;
use Storage;
use Validator;
use File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PictureController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //PRE: request may contain 
        //      id (The id of the model you are looking for)
        //      model (the model you are looking for, eg. App\Farm)
        if($request->has('id') && $request->has('model')){
            return Picture::where('attached_id', '=', $request->input('id'))
                ->where('attached_type', '=', $request->input('model'))
                ->get();
        }
        else{
            return Picture::get();
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
        //PRE: $request MUST contain the following
        //      image (the file being uploaded)
        //      attachedModel (model being attached to, eg. farm)
        //      attachedID = the ID of the attached model
        //POST: stores the picture in the DB
        $validator = Validator::make($request->all(), [
            'image' => 'required|file|mimes:jpeg,bmp,png,gif',
            'attachedModel' => 'required',
            'attachedID' => 'required|integer'
            ]);
        if(!$validator->fails() && $request->hasFile('image') && $request->file('image')->isValid()){
            //Formatting attached model
            $attachedModel = $request->input('attachedModel');
            $attachedModel = ucfirst($attachedModel);
            $atachedModel = "App/" . $attachedModel;

            //Creating the model
            $picture = Picture::create([
                'attached_id' => $request->input('attachedID'),
                'attached_type' => $request->input('attachedModel')
                ]);

            //Naming the file
            $file = $request->file('image');
            $attachedItem = $picture->attached;
            if($attachedItem == null){
                return Responses::BadRequest();
            }
            $modelName = substr($attachedModel, 4);
            $fileName = $modelName . "/" . $attachedItem->getKey() . '/' . time() . $file->getClientOriginalName();

            //Storing the image
            Storage::put(
                $fileName,
                File::get($file)
                );

            //Updating model and saving
            $picture->filePath = $fileName;
            $picture->save();
            return Responses::Created($picture->pictureID);
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
        //POST: returns the specific picture asked for
        $picture = Picture::find($id);
        if($picture != null){
            $image = $picture->getImagePath();
            return response()->file($image);
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
        //PRE: $request may contain the following
        //      image (required)
        //POST: stores the picture in the DB

        $picture = Picture::find($id);
        if($picture != null){
            $validator = Validator::make($request->all(), [
                'image' => 'required|file|mimes:jpeg,bmp,png,gif'
                ]);
            if(!$validator->fails() && $request->hasFile('image') && $request->file('image')->isValid()){
                //Naming the file
                $file = $request->file('image');
                $attachedItem = $picture->attached;
                if($attachedItem == null){
                    return Responses::BadRequest();
                }
                $modelName = substr($picture->attachedModel, 4);
                $fileName = $modelName . "/" . $attachedItem->getKey() . '/' . time() . $file->getClientOriginalName();
                

                //Storing the image
                Storage::put(
                    $fileName,
                    File::get($file)
                    );

                //Remove original image
                Storage::delete($picture->filePath);

                //Updating model and saving
                $picture->filePath = $fileName;
                $picture->save();
                return Responses::Created($picture->pictureID);
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
        //POST: removes the picture in filesystem and DB
        $picture = Picture::find($id);
        if($picture != null){
            Storage::delete($picture->filePath);
            $picture->delete();
            return Responses::Updated();
        }
        else{
            return Responses::DoesNotExist();
        }
    }
}
