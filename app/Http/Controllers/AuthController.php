<?php

namespace App\Http\Controllers;

// use App\Helpers\CatWikiHelpers;
// use App\Services\CatWikiService;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function userSignUp(Request $req)
    {
        // Validate the request body
        $this->requestValidator($req);

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

        // Remove password from collection
        // $user->forget('password');

        return Response::json(
            [
                "success" => true,
                "data" => $user->toArray()
            ],
            200
        );
    }

    public function userLogIn(Request $req)
    {
        // Validate the request body
        $this->requestValidator($req);

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

        return Response::json(
            [
                "success" => true,
                "data" => $user->toArray()
            ],
            200
        );
    }

    public function refreshToken(Request $req)
    {
        return Response::json(["success" => true], 200);
    }

    private function requestValidator(Request $req)
    {
        // Validate the request body
        $validator = Validator::make($req->all(), [
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required',
            'provider' => 'required',
        ]);

        if ($validator->fails()) {
            return Response::json([
                'success' => 'false',
                'error' => $validator->errors()
            ], 400);
        };
    }
};
