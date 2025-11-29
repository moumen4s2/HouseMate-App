<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Http\Controllers\HelperMethods;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyMail;
use Illuminate\Support\Facades\Hash;


class ForgetPasswordController extends Controller
{
    use HelperMethods;
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'max:255']
        ]);
        if ($validator->fails()) {
            return $this->fail($validator->errors(), 422);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return $this->fail('user not found !', 404);;
        }
        $otp = Str::random(6);
        Mail::to($request->email)->send(new VerifyMail($otp));
        $user->update([
            'otp' => Hash::make($otp),
            'expire_at' => now()->addMinutes(15),
        ]);

        return $this->success('OTP sent successfully to phone number !', null, 200);
    }
}
