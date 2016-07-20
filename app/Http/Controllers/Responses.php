<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class Responses{
	public static function NotAuthenticated(){
		return new Response('User is not authenticated', 401);
	}

	public static function PermissionDenied(){
		return new Response('User is not permitted', 403);
	}

	public static function Created($id = 0){
		return new Response(['ID' => $id], 201);
	}

	public static function Updated(){
		return new Response('Updated', 204);
	}

	public static function DoesNotExist($object = 'The object'){
		return new Response($object . ' does not exist', 410);
	}

	public static function BadRequest(){
		return new Response('Bad request', 400);
	}

	public static function AlreadyExists($object = 'The object'){
		return new Response($object . ' already exists', 409);
	}
}