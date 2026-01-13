<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Models\Apartment;


class ConversationController extends Controller
{
    use HelperMethods;

    public function start(Request $request)
    {
        $request->validate([
            'apartment_id' => 'required|exists:apartments,id'
        ]);

        $user = $request->user();
        $apartment = Apartment::findOrFail($request->apartment_id);

        if ($user->role !== 'tenant') {
            return $this->fail('Only tenants can start conversations', 403);
        }

        $conversation = Conversation::firstOrCreate([
            'tenant_id' => $user->id,
            'owner_id' => $apartment->owner_id,
            'apartment_id' => $apartment->id,
        ]);

        return $this->success('Conversation ready', $conversation);
    }
}
