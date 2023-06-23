<?php

namespace App\Http\Controllers;

// use App\Helpers\CatWikiHelpers;
// use App\Services\CatWikiService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class UserController extends Controller
{
    public function userDetails()
    {
        return Response::json(["success" => true], 200);
    }

    public function userDetailsUpdate(Request $req)
    {
        return Response::json(["success" => true], 200);
    }
};
