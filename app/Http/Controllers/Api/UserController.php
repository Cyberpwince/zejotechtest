<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PHPMailerController as Mailer;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Carbon\Carbon;
use App\Models\Withdrawal;
use App\Http\Controllers\WithdrawalController as Withdraw;

class UserController extends BaseController

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
        return $this->sendResponse($success, 'User registered successfully.');
    }

    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        if (filter_var($request->login, FILTER_VALIDATE_EMAIL)) {
            if (Auth::attempt(['email' => $request->login, 'password' => $request->password])) {
                $user = Auth::user();
                if ($user->ev==0){
                    return $this->sendError('User not Verified, Email Verification Required', ['error' => 'Login failed'], 400);
                }
                else{
                    $success['token'] = $user->createToken('UserLogin')->accessToken;
                $success['name'] = $user->username;
                return $this->sendResponse($success, 'User login successfully using Email.');
                }
            }
            else {
                return $this->sendError('Login Failed', ['error' => 'Login failed']);
            }
        }
        else if (is_numeric($request->login)) {
            if (Auth::attempt(['mobile' => $request->login, 'password' => $request->password])) {
                $user = Auth::user();
                if ($user->ev==0){
                    return $this->sendError('User not Verified, Email Verification Required', ['error' => 'Login failed'], 400);
                }
                else{
                    $success['token'] = $user->createToken('UserLogin')->accessToken;
                $success['name'] = $user->username;
                return $this->sendResponse($success, 'User login successfully using Mobile.');
                }
            }
            else {
                return $this->sendError('Login Failed', ['error' => 'Login failed']);
            }
        }
        else{
            if (Auth::attempt(['username' => $request->login, 'password' => $request->password])) {
                $user = Auth::user();
                if ($user->ev==0){
                    return $this->sendError('User not Verified, Email Verification Required', ['error' => 'Login failed'], 400);
                }
                else{
                    $success['token'] = $user->createToken('UserLogin')->accessToken;
                $success['name'] = $user->username;
                return $this->sendResponse($success, 'User login successfully using Username.');
                }
            }
            else {
                return $this->sendError('Login Failed', ['error' => 'Login failed'], 400);
            }
        }

    }
    public function requestVerification($userid){
        $user = User::where('id', $userid)->first();
        if (!$user) {
            return $this->sendError('User not found', ['error' => 'User not found']);
        }
        else if($user->ev ==1){
            return $this->sendError('User Verified', ['error' => 'User Verified'], 400);
        }
         else {
            $vcode = rand(100000, 999999);
            $subject = "Vixermail: Your Otp";
            $message = "Your OTP is : $vcode";
                $usr = new Mailer;

            $mail = $usr->composeEmail($user->email, $message, $subject, $user->username);

            if($mail['status']=="failed"){
               return $this->sendError('Failed', $mail);
            } else {
                $user->ver_code = $vcode;
                $user->ver_code_send_at = Carbon::now();
                $user->save();
                $success['name'] = $user->username;
                $success['email'] = $user->email;
                //$success['vcode'] = $vcode;
                return $this->sendResponse($success, 'Otp Sent Successfully');
            }
        }


    }
public function VerifyCode(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'otp_code' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        } else {
            $user = User::where('id', $request->user_id)->where('ver_code', $request->otp_code)->first();
            if(!$user){
                return $this->sendError('Invalid Code', ['error' => 'Invalid OTP']);
            }
            else{
                $user->ver_code = 0;
                $user->ev = 1;
                $user->save();
                $success['name'] = $user->username;
                $success['email'] = $user->email;
                //$success['vcode'] = $vcode;
                return $this->sendResponse($success, 'Otp Verified Successfully');
            }

        }
}
public function withdrawfund(Request $request){
    $validator = Validator::make($request->all(), [
        'user_id' => 'required|exists:users,id',
        'amount' => 'required',
    ]);

    if($validator->fails()){
            return $this->sendError("Withdrawal Failed", $validator->errors(), 400);
    }
    else{
        $user = User::where('id', $request->user_id)->first();
    if($user->balance < $request->amount){
    return $this->sendError('Withdrawal failed', ['error' => 'Insufficient Balance'], 201);
    } else {
                $w = new Withdraw;
                $request->request->add(['trx' => $w->createtrx()]);
                $userbalance = number_format(($user->balance - $request->amount), 2, ".", "");
                $withd = Withdrawal::create($request->all());
                $user->balance = $userbalance;
                $user->save();
                $success['name'] = $user->username;
                $success['email'] = $user->email;
                $success['withdrawal'] = [
                    'amount' =>$request->amount,
                    'trx' => $request->trx,
                    'status' => 'pending'
                ];


                return $this->sendResponse($success, 'Withdrawal successful');
            }

    }

}
public function withdrawall($userid){
    $user = User::where('id', $userid)->first();
    if (!$user) {
        return $this->sendError('User not found', ['error' => 'User not found']);
    }
    else {
        $userwithdraw = Withdrawal::where('user_id', $userid);
        if(!$userwithdraw->first()){
            return $this->sendError('No Withdrawal', ['error' => 'No record found'], 201);
        }
        else{
              return $this->sendResponse($userwithdraw->paginate(10), 'Record Found');

        }

    }
}
public function withdrawalid($userid, $withid){
    $user = User::where('id', $userid)->first();
    if (!$user) {
        return $this->sendError('User not found', ['error' => 'User not found']);
    }
    else {
        $userwithdraw = Withdrawal::where('user_id', $userid)->where('id', $withid)->first();
        if(!$userwithdraw){
            return $this->sendError('No Withdrawal', ['error' => 'No record found'], 201);
        }
        else{
              return $this->sendResponse($userwithdraw, 'Withdrawal Record found');

        }

    }
}


}
