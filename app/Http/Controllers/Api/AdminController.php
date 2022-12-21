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

    public function FundUser(Request $request){
        $validator = Validator::make($request->all(), [
                'admin_id' => 'required|exists:admins,id',
                'user_id' => 'required|exists:users,id',
                'amount' => 'required|numeric',

        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 201);
        }
        else{
            $user = User::where('id', $request->user_id)->first();
            $admin = Admin::where('id', $request->admin_id)->first();

            $oldbal = $user->balance;
            $balance = number_format(($user->balance + $request->amount), 2, ".", "");

            $user->balance = $balance;
            $user->save();
            $success['name'] = $user->username;
                $success['email'] = $user->email;
                $success['fund'] = [
                    'amount' => number_format($request->amount),
                    'by' => [
                        'id' => $admin->id,
                        'name' => $admin->name
                    ],
                    'balanceBefore' => number_format($oldbal),
                    'balanceAfter' => number_format($balance)
                ];


                return $this->sendResponse($success, 'User Fund successful');

        }
    }

}