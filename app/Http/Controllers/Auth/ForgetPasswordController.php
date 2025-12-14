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
use App\Services\OtpService;


class ForgetPasswordController extends Controller
{
    use HelperMethods;
     protected OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|min:10|max:15|regex:/^[0-9]+$/'
        ]);
        if ($validator->fails()) {
            return $this->fail($validator->errors(), 422);
        }

        $user = User::where('phone', $request->phone)->first();
        if (!$user) {
            return $this->fail('user not found !', 404);
        }
        $otp = (string) rand(10000, 99999);
        $this->otpService->attemptSendOtp($request->phone, $otp);
        $user->update([
            'otp' => Hash::make($otp),
            'expire_at' => now()->addMinutes(15),
        ]);

        return $this->success('OTP sent successfully to phone number !', null, 200);
    }
}
