<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Http\Controllers\HelperMethods;
use Illuminate\Support\Facades\Hash;
use App\Services\OtpService;
use Exception;

class RegisterController extends Controller
{
    use HelperMethods;
    protected OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    
    public function store(Request $request)
    {
try{
        $validationRules = [
            'first_name' => ['required', 'string', 'max:255'],
            'phone' => 'required|string|min:10|max:15|regex:/^[0-9]+$/',
            'last_name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', 'min:8', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[@$!%*#?&]/'],
            'role' => ['nullable', 'string', 'in:tenant,owner'],
            'avatar_url' => ['nullable', 'image', 'mimes:jpg,png,jpeg', 'max:4096'],
            'id_document_url' => ['required', 'image', 'mimes:jpg,png,jpeg', 'max:4096'],
            'date_of_birth' => ['required', 'date', 'before:today']
        ];
        $existingUser = User::where('phone', $request->phone)->first();
        if (!$existingUser || $existingUser->phone_verified_at !== null) {
            $validationRules['phone'] .= '|unique:users';
        }
        $validator = Validator::make($request->all(), $validationRules);
        if ($validator->fails()) {
            return $this->fail($validator->errors(), 422);
        }

        $validated = $validator->validated();
        if ($request->hasFile('avatar_url')) {
            $path = $request->file('avatar_url')->store('profiles', 'public');
            $validated['avatar_url'] = asset('storage/' . $path);
        }
        else{
            $validated['avatar_url'] = asset('storage/' . 'profiles/default-profile.jpg');
        }
        if ($request->hasFile('id_document_url')) {
            $path = $request->file('id_document_url')->store('profiles', 'public');
            $validated['id_document_url'] = asset('storage/' . $path);
        }

        $otp = (string) rand(10000, 99999);
        $this->otpService->attemptSendOtp($request->phone, $otp);
        $validated['password'] = Hash::make($request->password);
        if ($existingUser && $existingUser->phone_verified_at === null) {
            $existingUser->update([
                ...$validated,
                'otp' => /*Hash::make($otp)*/$otp,
                'expire_at' => now()->addMinutes(15),
            ]);
        } else {
        
            User::create([
                ...$validated,
                'otp' => /*Hash::make($otp)*/$otp,
                'expire_at' => now()->addMinutes(15),
            ]);
         }

        return  $this->success('OTP sent successfully to phone number, please verify your phone number !', null, 200);
}catch(Exception $e){
    return response($e);
}}
}
