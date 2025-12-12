<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Controllers\HelperMethods;

class LoginController extends Controller
{
    use HelperMethods;
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);
        if ($validator->fails()) {
            return $this->fail($validator->errors(), 422);
        }

        // if ( !Auth::attempt(['phone' => $request->phone, 'password' => $request->password])) {
        //     return response()->json(['errors' => 'Invalid credentials !'], 401);
        // }

        $user = User::where('phone', $request->phone)
            ->whereNotNull('email_verified_at')->where('is_approved', true)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return  $this->fail('Invalid credentials !', 401);
        }

        $token = $user->createToken('token')->plainTextToken;


        return $this->success('Logged in successfully !', ['token' => $token,'role'=>$user->role], 200);
    }

    public function destroy(Request $request)
    {
        $user = $request->user();
        if ($user) {

            $user->currentAccessToken()->delete();


            return $this->success('LogOut successfully !', null, 200);
        }

        return $this->fail('LogOut Error , The user does not exist !', 404);
    }
}
