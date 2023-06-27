<?php

namespace App\Http\Controllers;

use App\Models\RefreshToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class AuthController extends Controller
{

    public function userSignUp(Request $req)
    {
        // Validate the request body
        $validation_result = $this->requestValidator($req);
        if ($validation_result) {
            return $validation_result;
        }

        // Check if the user is already signed up
        if (User::where('email', $req->email)->exists()) {
            return Response::json([
                'success' => false,
                'message' => 'User already exists'
            ], 400);
        }

        // All good, create a new user
        $user = User::create([
            'email' => $req->email,
            'password' => Hash::make($req->password),
            'provider' => $req->provider
        ]);

        // Credentials
        $credentials = $req->only('email', 'password');
        $token = Auth::attempt($credentials);

        return Response::json(
            [
                "success" => true,
                "data" => $user->toArray(),
                "authToken" => $token
            ],
            200
        );
    }

    public function userLogIn(Request $req)
    {
        // Validate the request body
        $validation_result = $this->requestValidator($req);
        if ($validation_result) {
            return $validation_result;
        }

        // Check if the user is already exists
        if (User::where('email', $req->email)->doesntExist()) {
            return Response::json([
                'success' => false,
                'message' => 'User does not exist'
            ], 400);
        }

        // Retrieve the user matching the given email
        $user = User::where('email', $req->email)->first();

        // Compare password    
        if (!Hash::check($req->password, $user->password)) {
            return Response::json([
                'success' => false,
                'message' => 'Invalid Credentials'
            ], 400);
        }

        // Generate JWT with credentials
        $credentials = $req->only('email', 'password');
        $token = Auth::attempt($credentials);

        // Generate Refresh Token
        $refreshToken = RefreshToken::create([
            'user_id' => $user->id,
            'token' => Uuid::uuid4(),
            'expiry_date' => Date::now()->addDay(1)
        ]);

        return Response::json(
            [
                "success" => true,
                "data" => $user->toArray(),
                "authToken" => $token,
                "refreshToken" => $refreshToken->token
            ],
            200
        );
    }

    public function refreshToken(Request $req)
    {
        // Validate the request body
        $validator = Validator::make($req->all(), [
            'refreshToken' => 'required',
        ]);

        if ($validator->fails()) {
            return Response::json([
                'success' => false,
                'error' => $validator->errors()
            ], 400);
        };

        // Check if token exists
        if (RefreshToken::where('token', $req->refreshToken)->doesntExist()) {
            return Response::json([
                'success' => false,
                'message' => 'Invalid Token'
            ], 400);
        }

        // Retrieve the details matching the given token
        $user_token = RefreshToken::where('token', $req->refreshToken)->first();

        // If token is valid
        if (Date::now() > $user_token->expiry_date) {
            RefreshToken::where('token', $req->refreshToken)->delete();
            return Response::json([
                'success' => false,
                'message' => 'Token is expired'
            ], 400);
        }

        // Retrieve user matching the given token
        $user = User::where('id', $user_token->user_id)->first();

        $token = Auth::login($user);

        return Response::json([
            "success" => true,
            "authToken" => $token,
            "refreshToken" => $req->refreshToken
        ], 200);
    }

    private function requestValidator(Request $req)
    {
        // Validate the request body
        $validator = Validator::make($req->all(), [
            'email' => 'required|email|max:255|',
            'password' => 'required',
            'provider' => 'required',
        ]);

        if ($validator->fails()) {
            return Response::json([
                'success' => false,
                'error' => $validator->errors()
            ], 400);
        };
    }
};
