<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function userDetails()
    {
        $user = Auth::user();
        if (!$user) {
            return Response::json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }
        return Response::json(["success" => true, "data" => $user], 200);
    }

    public function userDetailsUpdate(Request $req)
    {
        // Validate the request body
        $validator = Validator::make($req->all(), [
            'name' => 'required|string',
            'bio' => 'required|string',
            'image' => 'required|string',
            'phoneNumber' => 'required|string',
        ]);

        if ($validator->fails()) {
            return Response::json([
                'success' => 'false',
                'error' => $validator->errors()
            ], 400);
        };
        $user = Auth::user();
        if (!$user) {
            return Response::json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }
        
        // Update with req body
        $user->name = $req->name;
        $user->bio = $req->bio;
        $user->image = $req->image;
        $user->phoneNumber = $req->phoneNumber;

        $user->save();

        return Response::json(["success" => true, "data" => $user], 200);
    }
};
