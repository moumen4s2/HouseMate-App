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
            'phone' => 'required|string|min:10|max:15|regex:/^[0-9]+$/',
            'otp' => ['required', 'string', 'min:5', 'max:5']
        ]);
        if ($validator->fails()) {
            return $this->fail($validator->errors(),422);
        }
        $user = User::where('phone', $request->phone)->first();
        if (!$user) {
            return $this->fail('User not found!',404);
        }
        if ($user->expire_at->gt(now()) && /*Hash::check($request->otp, $user->otp)*/ $request->otp === $user->otp) {
            $user->phone_verified_at = now();
            $user->otp = null;
            $user->expire_at = null;
            $user->save();
            return $this->success('phone number verified ,and user registerd successfuly,waiting admin to approverd your register !',['user'=>$user],201);
        }
        return $this->fail('OTP is not correct or expired!',400);
    }
}
