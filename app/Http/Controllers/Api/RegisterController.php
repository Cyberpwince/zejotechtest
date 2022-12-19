<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Validator;

class RegisterController extends BaseController

{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required',
            'lastname' => 'required',
            'username' => 'required|min:4|unique:users',
            'email' => 'required|email|unique:users',
            'mobile' => 'required|max:15|unique:users',
            'password' => 'required|min:6',
            'c_password' => 'required|same:password',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        else{
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('UserRegister')->accessToken;
        $success['name'] =  $user->username;
        return $this->sendResponse($success, 'User register successfully.');
    }

    }
    /**
     * Admin Register api
     *
     * @return \Illuminate\Http\Response
     */
     public function Adminregister(Request $request)
     {
         $validator = Validator::make($request->all(), [
             'name' => 'required',
             'email' => 'required|email',
             'password' => 'required',
             'c_password' => 'required|same:password',
         ]);
         if($validator->fails()){
             return $this->sendError('Validation Error.', $validator->errors());
         }
         $input = $request->all();
         $input['password'] = bcrypt($input['password']);
         $user = Admin::create($input);
         $success['token'] =  $user->createToken('MyApp')->accessToken;
         $success['name'] =  $user->name;
         return $this->sendResponse($success, 'Admin register successfully.');
     }
    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user = Auth::user();
            $success['token'] =  $user->createToken('UserLogin')-> accessToken;
            $success['name'] =  $user->name;
            return $this->sendResponse($success, 'User login successfully.');
        }
        else{
            return $this->sendError('Login Failed.', ['error'=>'Login failed']);
        }

    }

}