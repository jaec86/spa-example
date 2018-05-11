<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Facades\App\Services\JwtAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    public function __invoke(Request $request, $token)
    {
        $user = JwtAuth::check($token, 'reset');

        $request->validate(['password' => 'required|min:6|confirmed']);

        $user->password = bcrypt($request->password);
        $user->uuid = Str::uuid();
        $user->save();

        return response([
            'message' => 'Welcome!',
            'user' => $user,
            'access_token' => JwtAuth::createAccessToken($user->uuid),
            'refresh_token' => JwtAuth::createRefreshToken($user->uuid)
        ]);
    }
}