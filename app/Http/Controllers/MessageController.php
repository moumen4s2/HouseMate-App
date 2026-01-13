<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Events\MessageSent;

class MessageController extends Controller
{
    use HelperMethods;

    public function send(Request $request, Conversation $conversation)
    {
        $request->validate([
            'body' => 'required|string'
        ]);

        $user = $request->user();

        if (!in_array($user->id, [$conversation->tenant_id, $conversation->owner_id])) {
            return $this->fail('Unauthorized', 403);
        }

        $message = $conversation->messages()->create([
            'sender_id' => $user->id,
            'body' => $request->body
        ]);

        broadcast(new MessageSent($message))->toOthers();

        return $this->success('Message sent', $message);
    }

    public function index(Conversation $conversation)
    {
        return $this->success(
            'Messages fetched',
            $conversation->messages()->with('sender')->get()
        );
    }
}

