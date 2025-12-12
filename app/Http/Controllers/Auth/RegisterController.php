<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Http\Controllers\HelperMethods;
use App\Mail\VerifyMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;
use Exception;

class RegisterController extends Controller
{
    use HelperMethods;

    public function store(Request $request)
    {
        $validationRules = [
            'first_name' => ['required', 'string', 'max:255'],
            'phone' => 'required|string|min:10|max:15|regex:/^[0-9]+$/|unique:users',
            'email' => 'required|email|max:255',
            'last_name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', 'min:8', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[@$!%*#?&]/'],
            'role' => ['nullable', 'string', 'in:tenant,owner'],
            'avatar_url' => ['nullable', 'image', 'mimes:jpg,png,jpeg', 'max:4096'],
            'id_document_url' => ['required', 'image', 'mimes:jpg,png,jpeg', 'max:4096'],
            'date_of_birth' => ['required', 'date', 'before:today']
        ];
        $existingUser = User::where('email', $request->email)->first();
        if (!$existingUser || $existingUser->email_verified_at !== null) {
            $validationRules['email'] .= '|unique:users';
        }
        $validator = Validator::make($request->all(), $validationRules);
        if ($validator->fails()) {
            return $this->fail($validator->errors(), 422);
        }
        $validated = $validator->validated();
        if ($request->hasFile('avatar_url')) {
            $path = $request->file('avatar_url')->store('profiles', 'public');
            $validated['avatar_url'] = $path;
        }
        if ($request->hasFile('id_document_url')) {
            $path = $request->file('id_document_url')->store('profiles', 'public');
            $validated['id_document_url'] = $path;
        }

        $otp = Str::random(6);
        Mail::to($request->email)->send(new VerifyMail($otp));
        $validated['password'] = Hash::make($request->password);
        if ($existingUser && $existingUser->email_verified_at === null) {
            $existingUser->update([
                ...$validated,
                'otp' => Hash::make($otp),
                'expire_at' => now()->addMinutes(15),
            ]);
        } else {

            User::create([
                ...$validated,
                'otp' => Hash::make($otp),
                'expire_at' => now()->addMinutes(15),
            ]);
        }

        return  $this->success('OTP sent successfully to email, please verify your email !.', null, 200);
    }
}
