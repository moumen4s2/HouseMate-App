<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperMethods;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class UpdatePersonalInfoController extends Controller
{
    use HelperMethods;
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'avatar_url' => ['nullable', 'image', 'mimes:jpg,png,jpeg', 'max:4096'],
            'id_document_url' => ['nullable', 'image', 'mimes:jpg,png,jpeg', 'max:4096'],
            'date_of_birth' => ['nullable', Rule::date()/*->format('dd-mm-yyyy')*/->before(today())],
            'dir' => ['nullable', 'in:rtl,ltr'],
            'mode' => ['nullable', 'in:dark,light']
        ]);
        if ($validator->fails()) {
            return $this->fail($validator->errors(), 422);
        }
        $validated = $validator->validated();
        $user = $request->user();
        if ($request->hasFile('avatar_url')) {
            if ($user->avatar_url !== 'profiles/default-profile.jpg' && Storage::disk('public')->exists($user->avatar_url)) {
                Storage::disk('public')->delete($user->avatar_url);
            }
            $path = $request->file('avatar_url')->store('profiles', 'public');
            $validated['avatar_url'] = asset('storage/' . $path);
        }
        if ($request->hasFile('id_document_url')) {
            if (Storage::disk('public')->exists($user->id_document_url)) {
                Storage::disk('public')->delete($user->id_document_url);
            }
            $path = $request->file('id_document_url')->store('profiles', 'public');
            $validated['id_document_url'] = asset('storage/' . $path);
        }
        $user->update($validated);

      /*  $responseUser = $user->toArray();

        $responseUser['avatar_url'] = $user->avatar_url
            ? asset('storage/' . $user->avatar_url)
            : null;

        $responseUser['id_document_url'] = $user->id_document_url
            ? asset('storage/' . $user->id_document_url)
            : null;

        return $this->success('information updated !', $responseUser, 200);*/

        return  $this->success('information updated !', $user, 200);
    }
}
