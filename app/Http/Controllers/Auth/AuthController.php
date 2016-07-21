<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;
use Auth;

use App\User;
//use Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
// use Illuminate\Foundation\Auth\ThrottlesLogins;
// use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller
{
	protected $guard = "web";

    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
        Handles an authentication attempt
    */

    //use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware($this->guestMiddleware(), ['except' => 'logout']);
    }

    public function authenticate(Request $request){
        $credentials = ['email' => $request->input('email'), 'password' => $request->input('password')];
        try{
            if(! $token = JWTAuth::attempt($credentials)) {
                return $this->response->error(['error' => 'user credentials invalid'], 401);
            }
        }
        catch (JWTException $exception) {
            return $this->response->error(['error' => 'Something went wrong!'], 500);
        }

        return $this->response->item(compact('token'));
    }

    // /**
    //  * Get a validator for an incoming registration request.
    //  *
    //  * @param  array  $data
    //  * @return \Illuminate\Contracts\Validation\Validator
    //  */
    // protected function validator(array $data)
    // {
    //     return Validator::make($data, [
    //         'firstName' => 'required|max:255',
    //         'lastName' => 'required|max:255',
    //    		'middleInitial' => 'max:1',
    //         'email' => 'required|email|max:255|unique:user',
    //         'password' => 'required|min:6'//|confirmed',
    //         //'userType' => 'required|in:student,instructor'
    //     ]);
    // }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'email' => $data['email'],
            'password' => bcrypt($data['password'])
        ]);
    }
}
