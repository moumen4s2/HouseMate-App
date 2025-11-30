<?php

namespace App\Http\Controllers;

use App\Http\Controllers\HelperMethods;
use App\Models\Booking;
use App\Models\Apartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class BookingController extends Controller
{
    use HelperMethods;

    public function index(Request $request)
    {
        $user = $request->user();
        $query = Booking::with(['apartment.images', 'tenant', 'apartment.owner']); // تحميل العلاقات الضرورية

        if ($user->role === 'tenant') {
            $query->where('tenant_id', $user->id);
        } elseif ($user->role === 'owner') {
            $query->whereHas('apartment', function ($q) use ($user) {
                $q->where('owner_id', $user->id);
            });
        } else {
            return $this->fail('Unauthorized access to bookings list.', 403);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->orderBy('start_date', 'desc')->paginate(10); 

        return $this->success('Bookings fetched successfully', $bookings);
    }

    public function store(Request $request)
    {
        $tenant = $request->user();
        
        if ($tenant->role !== 'tenant' || !$tenant->is_approved) {
            return $this->fail('Unauthorized. Only approved tenants can book.', 403);
        }
        
        $validator = Validator::make($request->all(), [
            'apartment_id' => 'required|exists:apartments,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'total_price' => 'required|numeric|min:0',
            'payment_info' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->fail($validator->errors(), 422);
        }

        $validated = $validator->validated();

        $conflictingBookingExists = Booking::where('apartment_id', $validated['apartment_id'])
            ->where('status', 'confirmed')
            ->where(function ($query) use ($validated) {
                $query->whereBetween('start_date', [$validated['start_date'], $validated['end_date']])
                    ->orWhereBetween('end_date', [$validated['start_date'], $validated['end_date']])
                    ->orWhere(function ($query) use ($validated) {
                        $query->where('start_date', '<', $validated['start_date'])
                                ->where('end_date', '>', $validated['end_date']);
                    });
            })
            ->exists();

        if ($conflictingBookingExists) {
            return $this->fail('This apartment is already booked for the selected dates.', 409); 
        }

        $booking = Booking::create([
            'apartment_id' => $validated['apartment_id'],
            'tenant_id' => $tenant->id,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'total_price' => $validated['total_price'],
            'payment_info' => $validated['payment_info'],
            'status' => 'pending', 
        ]);

        return $this->success('Booking request placed successfully! Awaiting owner confirmation.', $booking, 201);
    }

    public function update(Request $request, Booking $booking)
    {
        $user = $request->user();

        if ($user->id !== $booking->apartment->owner_id) {
            return $this->fail('Forbidden. You are not the owner of this apartment.', 403);
        }
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:confirmed,rejected,cancelled', 
        ]);

        if ($validator->fails()) {
            return $this->fail($validator->errors(), 422);
        }

        $newStatus = $request->status;

        $booking->update(['status' => $newStatus]);

        if ($newStatus === 'confirmed') {
            Booking::where('apartment_id', $booking->apartment_id)
                ->where('id', '!=', $booking->id)
                ->where('status', 'pending')
                ->where(function ($query) use ($booking) {
                    $query->whereBetween('start_date', [$booking->start_date, $booking->end_date])
                        ->orWhereBetween('end_date', [$booking->start_date, $booking->end_date]);
                })
                ->update(['status' => 'rejected']);
        }

        return $this->success("Booking status updated to {$newStatus}", $booking);
    }
}
