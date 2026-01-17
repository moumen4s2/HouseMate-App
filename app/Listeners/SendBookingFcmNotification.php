<?php

namespace App\Listeners;

use App\Events\BookingStatusChanged;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Notification;

class SendBookingFcmNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(BookingStatusChanged $event): void
    {
         $booking = $event->booking;
        $message = $event->message;

        //$booking->load('tenant.fcmTokens');
        $user = $booking->tenant;
        
        Notification::create([
    'user_id' => $user->id,
    'type' => 'booking_status',
    'message' => $message,
    'payload' => json_encode([
        'booking_id' => (string) $booking->id,
        'status' => $booking->status,
    ]),
]);

        // foreach ($booking->tenant->fcmTokens as $token) {
        $token=$booking->tenant->fcmTokens;
        if($token){
            // app(\App\Services\FcmService::class)->send(
            //     $token->token,
            //     'تحديث حالة الحجز',
            //     $message,
            //     [
            //         'booking_id' => (string) $booking->id,
            //         'status' => $booking->status,
            //         'type' => 'booking_status'
            //     ]
            // );
        // }
    }}
}
