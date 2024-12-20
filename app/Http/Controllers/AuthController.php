<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegistrationRequest;
use App\Http\Responses\Response;
use App\Models\User\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Register :

    public function register(RegistrationRequest $request)
    {
        $user = User::create([
            "FirstName" => $request->FirstName,
            "LastName" => $request->LastName,
            "email" => $request->email,
            "password" => Hash::make($request->password),
        ]);

        $token =JWTAuth::attempt([
            'email' => $request->email,
            'password' => $request->password
        ]);

        if ($user) {
            return Response::AuthSuccess("Registration successfully",$user,$token);
        } else {
            return Response::Message401("Registration failed..!");
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Login :
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user){
            $token = JWTAuth::attempt([
                'email' => $request->email,
                'password' => $request->password
            ]);

            if($token){
                return Response::AuthSuccess("User login successfully",$user,$token);
            } else{
                return Response::Message422("Password does not match.");
            }
        }else{
            return Response::Message401("The email dose not match ");
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Logout :
    public function logout(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);
        try {
            // invalidate token
            JWTAuth::invalidate(JWTAuth::getToken());
            return Response::logout(true,'Logout successfully',200);
        } catch (JWTException $e) {
            return Response::logout(false,'Failed to logout..!',500);
        }
    }


}
