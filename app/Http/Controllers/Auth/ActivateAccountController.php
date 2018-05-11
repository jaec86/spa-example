<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Facades\App\Services\JwtAuth;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ActivateAccountController extends Controller
{
    public function __invoke(Request $request, $token)
    {
        $user = $this->checkToken($token);

        $user->activated_at = now();
        $user->uuid = Str::uuid();
        $user->save();

        return response()->json([
            'message' => 'Welcome!',
            'user' => $user,
            'access_token' => JwtAuth::createAccessToken($user->uuid),
            'refresh_token' => JwtAuth::createRefreshToken($user->uuid)
        ]);
    }

    protected function checkToken($token)
    {
        if (! JwtAuth::verify($token)) {
            throw new AuthenticationException("The activation code is invalid.");
        }

        if (! $user = JwtAuth::getUser($token)) {
            throw new AuthenticationException('The user does not exist.');
        }

        return $user;
    }
}
