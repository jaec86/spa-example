<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ResetPassword;
use App\User;
use Facades\App\Services\JwtAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class ForgotPasswordController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate($this->rules());

        $user = User::where('email', $request->email)->first();
        $token = JwtAuth::createResetToken($user->uuid);
        Mail::to($user->email)->send(new ResetPassword($user, $token));

        return response()->json(['message' => 'We have send you an email with the reset password link.']);
    }

    protected function rules()
    {
        return [
            'email' => [
                'required',
                'email',
                Rule::exists('users', 'email')->where(function ($query) {
                    $query->whereNotNull('activated_at');
                })
            ]
        ];
    }
}
