<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\ApartmentImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

use App\Http\Controllers\HelperMethods;

class ApartmentController extends Controller
{
    use HelperMethods;

    private function applyFilters($query, Request $request)
    {
        if ($request->has('city')) {
            $query->where('city', $request->city);
        }
        if ($request->has('province')) {
            $query->where('province', $request->province);
        }

        if ($request->has('price_max') && is_numeric($request->price_max)) {
            $query->where('price', '<=', $request->price_max);
        }

        if ($request->has('rooms_min') && is_numeric($request->rooms_min)) {
            $query->where('rooms', '>=', $request->rooms_min);
        }

        if ($request->has('guests_min') && is_numeric($request->guests_min)) {
        $query->where('guests', '>=', $request->guests_min);
        }
    
        $query->where('is_active', true);

        return $query;
    }

    public function index(Request $request)
    {
        $query = Apartment::with(['images', 'owner']);
        $query = $this->applyFilters($query, $request);
        $apartments = $query->paginate(15); 
        return $this->success('Apartments fetched successfully', $apartments);
    }

    public function show(Apartment $apartment)
    {
        $apartment->load([
            'images', 
            'owner',
            'reviews.tenant' 
        ]);

        if (!$apartment->is_active) {
            return $this->fail('Apartment not found or inactive!', 404);
        }

        return $this->success('Apartment details fetched successfully', $apartment);
    }

        public function store(Request $request)
    {
        $user = $request->user();

        if (!$user || $user->role !== 'owner' || !$user->is_approved) {
            return $this->fail('Unauthorized. Only approved owners can list apartments.', 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'address' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'rooms' => 'required|integer|min:1',
            'guests' => 'required|integer|min:1',
            'images' => 'required|array|min:1',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:4096',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return $this->fail($validator->errors(), 422);
        }

        $apartment = $user->apartments()->create([
            'title' => $request->title,
            'description' => $request->description,
            'city' => $request->city,
            'province' => $request->province,
            'address' => $request->address,
            'price' => $request->price,
            'rooms' => $request->rooms,
            'guests' => $request->guests,
            'is_active' => $request->is_active ?? true,
        ]);

        foreach ($request->file('images') as $index => $file) {
            $path = $file->store('apartments', 'public');

            $apartment->images()->create([
                'url' => $path,
                'is_main' => ($index === 0),
            ]);
        }
        $apartment->load('images');
        return $this->success('Apartment created successfully!', $apartment, 201);
    }


        public function update(Request $request, Apartment $apartment)
    {
        $user = $request->user();

        if ($user->id !== $apartment->owner_id) {
            return $this->fail('Forbidden. You do not own this apartment.', 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'city' => 'sometimes|string|max:100',
            'province' => 'sometimes|string|max:100',
            'address' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric|min:0',
            'rooms' => 'sometimes|integer|min:1',
            'guests' => 'sometimes|integer|min:1',
            'is_active' => 'sometimes|boolean',

            'images' => 'sometimes|array|min:1',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:4096',

            'delete_images' => 'sometimes|array',
            'delete_images.*' => 'integer|exists:apartment_images,id',

            'main_image' => 'sometimes|integer|exists:apartment_images,id',
        ]);

        if ($validator->fails()) {
            return $this->fail($validator->errors(), 422);
        }

        $apartment->update($validator->validated());

        if ($request->has('delete_images')) {
            $imagesToDelete = ApartmentImage::where('apartment_id', $apartment->id)
                ->whereIn('id', $request->delete_images)
                ->get();

            foreach ($imagesToDelete as $img) {
                Storage::disk('public')->delete($img->url);
                $img->delete();
            }
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('apartments', 'public');

                $apartment->images()->create([
                    'url' => $path,
                    'is_main' => false,
                ]);
            }
        }

        if ($request->has('main_image')) {
            ApartmentImage::where('apartment_id', $apartment->id)->update(['is_main' => false]);

            ApartmentImage::where('id', $request->main_image)
                ->where('apartment_id', $apartment->id)
                ->update(['is_main' => true]);
        }

        $apartment->load('images');

        return $this->success('Apartment updated successfully', $apartment);
    }

    public function destroy(Apartment $apartment)
    {
        $user = Auth::user(); 

        if ($user->id !== $apartment->owner_id) {
            return $this->fail('Forbidden. You do not own this apartment.', 403);
        }

        foreach ($apartment->images as $image) {
            Storage::disk('public')->delete($image->url);
        }

        $apartment->delete();
        return $this->success('Apartment deleted successfully', null, 200);
    }

}