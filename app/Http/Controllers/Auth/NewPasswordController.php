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
            'otp' => ['required', 'string', 'min:5', 'max:5'],
            'phone' => 'required|string|min:10|max:15|regex:/^[0-9]+$/',
            'password' => ['required', 'confirmed', 'min:8', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[@$!%*#?&]/','not_regex:/\s/'],
        ]);
        if ($validator->fails()) {
            return $this->fail($validator->errors(), 422);
        }

        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return $this->fail("user not found !", 404);
        }
        if ($user->expire_at->gt(now()) && Hash::check($request->otp, $user->otp)) {

            $user->update([
                'password' => Hash::make($request->password),
                'otp' => null,
                'expire_at' => null
            ]);
            return $this->success('password update successfuly !', null, 201);
        }

        return $this->fail('OTP is not correct or expired!', 400);
    }
}
