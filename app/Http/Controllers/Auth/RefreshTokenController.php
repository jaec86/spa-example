<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Facades\App\Services\JwtAuth;
use Illuminate\Http\Request;

class RefreshTokenController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = JwtAuth::check($request->bearerToken(), 'refresh');

        return response()->json([
            'message' => 'The access code was refreshed.',
            'user' => $user,
            'access_token' => JwtAuth::createAccessToken($user->uuid),
            'refresh_token' => JwtAuth::createRefreshToken($user->uuid)
        ]);
    }
}
