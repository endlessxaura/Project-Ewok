<?php 

namespace App\Http\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;
use Illuminate\Http\Response;
use Auth;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;
use App\User;
use Hash;
use Validator;

class AuthenticateController extends Controller
{
    public function authenticate(Request $request)
    {

        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt(['email' => $request->email, 'password' => $request->password])) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        // all good so return the token
        return response()->json(compact('token'));
    }

    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:user,email',
            'password' => 'required|confirmed',
            'firstName' => 'required',
            'lastName' => 'required'
            ]);

        if($validator->fails()){
            return response()->json(['error' => 'registration failed'], 400);
        }

        User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'firstName' => $request->input('firstName'),
            'lastName' => $request->input('lastName')
            ]);
    }

    public function refreshToken(Request $request){
        $token = JWTAuth::getToken();
        if(!$token){
            return $this->response->errorUnauthorized('Token is invalid');
        }

        try {
            $token = JWTAuth::refresh($token);
        }   
        catch(JWTException $ex){
            return response()->json(["error" => 'Bad token']);
        }

        return response()->json(compact('token'));
    }

    public function destroyToken(Request $request){
        $user = JWTAuth::parseToken()->authenticate();
        if(!$user){
            $this->response->error('Token is invalid');
        }  
        JWTAuth::parseToken()->invalidate();
    }

    public function getAuthenticatedUser()
    {
        try {

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['error' => 'user_not_found'], 404);
            }

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['error' => 'token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['error' => 'token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['error' => 'token_absent'], $e->getStatusCode());

        }

        $user->reviews;
        $user->geolocations;
        $user->pictures;
        $user->password = null;

        // the token is valid and we have found the user via the sub claim
        return $user;
    }
}