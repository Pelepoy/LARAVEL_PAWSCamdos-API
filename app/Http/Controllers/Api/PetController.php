<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use App\Http\Requests\StorePetRequest;
use App\Http\Requests\UpdatePetRequest;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PetController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show'])
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return [
            'status' => 'success',
            'data' => Pet::all(),
        ];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePetRequest $request)
    {
        try {
            $data = $request->validated();

            $data['profile_image_url'] = $request->hasFile('profile_image_url')
                ? Storage::url($request->file('profile_image_url')->store('dog_image'))
                : null;

            $dog = $request->user()->dogs()->create($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Dog information was saved successfully',
                'data' => $dog
            ], 201);
        } catch (\Exception $e) {
            // Log::error('Error saving dog information' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while saving dog information',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Pet $pet)
    {
        return response()->json([
            'status' => 'success',
            'data' => $pet
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePetRequest $request, Pet $pet)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pet $pet)
    {
        //
    }
}