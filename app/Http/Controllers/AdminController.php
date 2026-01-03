<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;

class AdminController extends Controller
{
    use HelperMethods;

    public function showRegisters()

    {
        $users = User::whereNotNull('phone_verified_at')->where('is_approved', false)->where('role', "!=", "admin")->paginate(10);
        return $this->success('done', $users, 200);
        
    }

    // public function approvedRegistration($user_id)
    // {
    //     $user = User::find($user_id);
    //     if (!$user) {
    //         return $this->fail('user not found !', 404);
    //     }
    //     if ($user->email_verified_at && !$user->is_approved && $user->role !== 'admin') {
    //         $user->is_approved = true;
    //         $user->save();
    //         return $this->success('Registration approved !', $user, 201);
    //     }
    //     return $this->fail('email not verified or Registration has already been approved !', 400);}


    public function approvedRegistration(User $user)
    {

        if ($user->phone_verified_at && !$user->is_approved && $user->role !== 'admin') {
            $user->is_approved = true;
            $user->save();
            return $this->success('Registration approved !', $user, 201);
        }
        return $this->fail('phone not verified or Registration has already been approved !', 400);
        
    }

       public function deleteRegistration(User $user)
    {
        $user->delete();
        return $this->success('Registration deleted successfully!',null, 200);
    }
}
