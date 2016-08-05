<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class Responses{
	public static function NotAuthenticated(){
		return new Response(['error' => 'User is not authenticated'], 401);
	}

	public static function PermissionDenied(){
		return new Response(['error' => 'User is not permitted'], 403);
	}

	public static function Created($id = 0){
		return new Response(['message' => 'The object was created', 'ID' => $id], 201);
	}

	public static function Updated(){
		return new Response('Updated', 204);
	}

	public static function DoesNotExist($object = 'The object'){
		return new Response(['error' => $object . ' does not exist'], 410);
	}

	public static function BadRequest(){
		return new Response(['error' => 'Bad request'], 400);
	}

	public static function AlreadyExists($object = 'The object'){
		return new Response(['error' => $object . ' already exists'], 409);
	}

	public static function TooFar(){
		return new Response(['error' => 'Too far away'], 400);
	}
}