<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Exception;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {   
        User::create([
        'role'=>'admin',
        'first_name'=>'admin',
        'last_name'=>'admin',
        'phone'=>"0000000000",
        'password'=>Hash::make("0000000000"),
        'id_document_url'=>asset('storage/' . 'profiles/default-profile.jpg'),
        'avatar_url'=>asset('storage/' . 'profiles/default-profile.jpg'),
        'is_approved'=>true,
        'phone_verified_at'=>now(),
        'date_of_birth'=>'2001-1-1'
        ]);
    }
}
