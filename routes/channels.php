<?php

use Illuminate\Support\Facades\Broadcast;

use App\Models\Conversation;

Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    $conversation = Conversation::find($conversationId);

    if (!$conversation) return false;

    return $user->id === $conversation->tenant_id
        || $user->id === $conversation->owner_id;
});



// Broadcast::channel('chat.{userId}', function ($user, $userId) {
//     return (int) $user->id === (int) $userId;
// });

// Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });
