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

            if ($request->hasFile('profile_image_url')) {
                $filePath = $request->file('profile_image_url')->store('pet_image');
                $data['profile_image_url'] = Storage::url($filePath);
                $data['file_name'] = $filePath;
            }

            $pet = $request->user()->pets()->create($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Pet information was saved successfully',
                'data' => $pet
            ], 201);
        } catch (\Exception $e) {
            // Log::error('Error saving dog information' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while saving pet information',
                'errors' => $e->getMessage()
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
        try {
            Gate::authorize('scopeOwner', $pet);
            $data = $request->validated();
            // Log::info('Validated Data: ' . json_encode($data));
            if ($request->hasFile('profile_image_url')) {
                if ($pet->file_name) {
                    Storage::disk()->delete($pet->file_name);
                }
                $filePath = $request->file('profile_image_url')->store('pet_image');
                $data['file_name'] = $filePath;
                $data['profile_image_url'] = Storage::url($filePath);
            }

            $pet->update($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Pet information updated successfully',
                'data' => $pet
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while updating pet information',
                'errors' => $e->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pet $pet) // Soft delete
    {
        Gate::authorize('scopeOwner', $pet);
        $pet->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Pet deleted successfully',
        ]);
    }

    public function forceDelete(Pet $pet) // Force delete
    {
        Gate::authorize('scopeOwner', $pet);
        $pet->forceDelete();
        return response()->json([
            'status' => 'success',
            'message' => 'Pet force deleted successfully',
        ]);
    }
}