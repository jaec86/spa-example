<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Rules\CheckPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PasswordController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke(Request $request)
    {
        $this->validate($request, $this->rules());

        $this->savePassword($request->new_password);

        return response()->json(['message' => 'Your new password was saved.']);
    }

    protected function rules()
    {
        return [
            'old_password' => [
                'required', 
                function ($attribute, $value, $fail) {
                    if (! Hash::check($value, Auth::user()->password)) {
                        return $fail('Your old password is invalid.');
                    }
                }
            ],
            'new_password' => 'required|min:6|confirmed',
        ];
    }

    protected function savePassword($password)
    {
        $user = Auth::user();
        $user->password = bcrypt($password);
        $user->uuid = Str::uuid();

        $user->save();
    }
}
