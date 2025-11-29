<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Http\Controllers\HelperMethods;
use Illuminate\Support\Facades\Hash;

class VerifyController extends Controller
{
    use HelperMethods;
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'max:255'],
            'otp' => ['required', 'string', 'min:6', 'max:6']
        ]);
        if ($validator->fails()) {
            return $this->fail($validator->errors(),422);
        }
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return $this->fail('User not found!',404);
        }
        if ($user->expire_at->gt(now()) && Hash::check($request->otp, $user->otp) ) {
            $user->email_verified_at = now();
            $user->otp = null;
            $user->expire_at = null;
            $user->save();
            $token = $user->createToken('token')->plainTextToken;
            return $this->success('email verified ,and user registerd successfuly !',['token' => $token],201);
        }
        return $this->fail('OTP is not correct or expired!',400);
    }
}
