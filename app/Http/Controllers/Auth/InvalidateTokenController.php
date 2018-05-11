<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Facades\App\Services\JwtAuth;
use Illuminate\Http\Request;

class InvalidateTokenController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate(['token' => 'required']);
        
        JwtAuth::blacklist($request->token);

        return response()->json(['message' => 'The code was invalidated.']);
    }
}
