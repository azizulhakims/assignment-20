<?php

namespace App\Http\Controllers;

use App\Helper\JWTToken;
use App\Mail\OTPMail;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;


class UserController extends Controller
{
    function UserRegistration(Request $request)
    {

        try {

            User::create([
                'firstName'=> $request->input('firstName'),
                'lastName'=> $request->input('lastName'),
                'email'=> $request->input('email'),
                'mobile'=> $request->input('mobile'),
                'password'=> $request->input('password'),

            ]);

            return response()->json([
                'status' => 'success',
                'massage'=>'User Registration Successfully'
            ], status: 200);

        }
        catch (Exception $e){

            return response()->json([
                'status' => 'failed',
//                'massage'=> $e->getMessage()
                'massage' => 'User Registration Failed'
            ], status: 200);

        }

    }

    function UserLogin(Request $request)
    {
       $count= User::where('email','=',$request->input('email'))
             ->where('password','=',$request->input('password'))
            ->count();

       if($count==1){
           //user

           $token=JWTToken::CreateToken($request->input('email'));
           return response()->json([
               'status' => 'success',
               'message' => 'User Login Successful',
               'token' => $token
           ], status: 200);
       }
       else{
           return response()->json([
               'status' => 'failed',
               'message' => 'unauthorized'
           ], status: 200);

       }

    }

    function SendOTPCode(Request $request){

        $email=$request->input('email');
        $otp=rand(1000,9999);
        $count=User::where('email','=',$email)->count();

        if ($count==1){
            //OTP Email Address
            Mail::to($email)->send(new OTPMail($otp));
            //OTP code table Update
            User::where('email','=',$email)->update(['opt'=>$otp]);

            return response()->json([
                'status'=>'success',
                'message'=> '4 Digit OTP code has bees send to your email'
            ],200);

        }

        else{
            return response()->json([
                'status' => 'failed',
                'message' => 'unauthorized'
            ], status: 200);
        }

    }

}

