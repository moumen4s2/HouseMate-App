<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Apartment;

class FavoriteController extends Controller
{
    use HelperMethods;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        
        $user = $request->user();

        if ($user->role !== 'tenant') {
            return $this->fail('Unauthorized. Only tenants can view favorites.', 403);
        }
        
        $apartments = $user->favorites()->with(['apartment.images'])->paginate(10);
        return $this->success('Favorite apartments fetched successfully!', $apartments, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Apartment $apartment)
    {
        $user = $request->user();

        if ($user->role !== 'tenant') {
            return $this->fail('Unauthorized. Only tenants can add favorites.', 403);
        }

        if ($user->favorites()->where('apartment_id', $apartment->id)->exists()) {
            return $this->fail('Apartment is already in favorites.', 409);
        }

        $user->favorites()->create([
            'apartment_id' => $apartment->id
        ]);

        return $this->success('Apartment added to favorites successfully!', $apartment, 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Apartment $apartment)
    {
        $user = $request->user();

        if ($user->role !== 'tenant') {
            return $this->fail('Unauthorized. Only tenants can remove favorites.', 403);
        }
        $favorite = $user->favorites()->where('apartment_id', $apartment->id)->first();

        if (!$favorite) {
            return $this->fail('Apartment not found in favorites.', 404);
        }

        $favorite->delete();
        return $this->success('Apartment removed from favorites successfully!', null, 200);
    }
}
