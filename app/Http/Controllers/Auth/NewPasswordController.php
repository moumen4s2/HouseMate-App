<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Http\Controllers\HelperMethods;
use Illuminate\Support\Facades\Hash;

class NewPasswordController extends Controller
{
use HelperMethods;
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => ['required', 'string', 'min:6', 'max:6'],
            'email' => ['required', 'email'],
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/'
            ],
        ]);
        if ($validator->fails()) {
            return $this->fail($validator->errors(),422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
           return $this->fail("user not found !",404);
        }
        if ($user->expire_at->gt(now()) && Hash::check($request->otp, $user->otp) ) {

            $user->update([
                'password' => Hash::make($request->password),
                'otp' => null,
                'expire_at' => null
            ]);
           return $this->success('password update successfuly !',null,201);
        }

       return $this->fail('OTP is not correct or expired!',400);
    }
}
