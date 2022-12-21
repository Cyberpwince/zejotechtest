<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Validator;

class AdminController extends BaseController

{
    /**
     * Admin Register api
     *
     * @return \Illuminate\Http\Response
     */
     public function Adminregister(Request $request)
     {
         $validator = Validator::make($request->all(), [
             'name' => 'required',
             'email' => 'required|email|unique:admins',
             'password' => 'required',
             'c_password' => 'required|same:password',
         ]);
         if($validator->fails()){
             return $this->sendError('Validation Error.', $validator->errors());
         }
         $input = $request->all();
         $input['password'] = bcrypt($input['password']);
         $user = Admin::create($input);
         $success['token'] =  $user->createToken('AdminRegister')->accessToken;
         $success['name'] =  $user->name;
         return $this->sendResponse($success, 'Admin registered successfully.');
     }
     /**
     * Admin Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function Adminlogin(Request $request)
    {
        if(Auth::guard('apiadmin')->attempt(['email' => $request->email, 'password' => $request->password])){
            $user = Auth::guard('apiadmin')->user();
            $success['token'] =  $user->createToken('AdminLogin')-> accessToken;
            $success['name'] =  $user->name;
            return $this->sendResponse($success, 'Admin login successfully.');
        }
        else{
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);

        }

    }

}
