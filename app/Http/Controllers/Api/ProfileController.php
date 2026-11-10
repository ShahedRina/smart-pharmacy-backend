<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\AccountResource;

class ProfileController extends Controller
{
    // Display authenticated user's profile
    public function show(Request $request)
    {
        return new AccountResource($request->user());
    }
}
