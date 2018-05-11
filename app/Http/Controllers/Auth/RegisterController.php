<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ActivateAccount;
use App\User;
use Facades\App\Services\JwtAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate($this->rules());

        $this->createUser($request);

        return response()->json(['message' => 'We have send you an email with the activation link.']);
    }

    protected function rules()
    {
        return [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ];
    }

    protected function createUser($request)
    {
        $user = new User;
        $user->uuid = Str::uuid();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        $token = JwtAuth::createActivationToken($user->uuid);
        Mail::to($user->email)->send(new ActivateAccount($user, $token));

        return $user;
    }
}
