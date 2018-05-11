<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Facades\App\Services\JwtAuth;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use ThrottlesLogins;

    public function __invoke(Request $request)
    {
        $this->validateLogin($request);

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        if (Auth::guard()->attempt($request->only($this->username(), 'password'))) {
            return $this->sendLoginResponse();
        }

        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    public function username()
    {
        return 'email';
    }

    protected function validateLogin($request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);
    }

    protected function sendLockoutResponse($request)
    {
        $seconds = $this->limiter()->availableIn(
            $this->throttleKey($request)
        );

        return response()->json([
            'message' => __('auth.throttle', ['seconds' => $seconds]),
        ], 423);
    }

    protected function sendLoginResponse()
    {
        $user = Auth::user();

        if (! $user->activated_at) {
            throw new AuthorizationException('The account has not been activated.');
        }

        return response()->json([
            'message' => 'Welcome!',
            'user' => $user,
            'access_token' => JwtAuth::createAccessToken($user->uuid),
            'refresh_token' => JwtAuth::createRefreshToken($user->uuid)
        ]);
    }

    protected function sendFailedLoginResponse()
    {
        return response()->json([
            'message' => __('auth.failed')
        ], 422);
    }
}
