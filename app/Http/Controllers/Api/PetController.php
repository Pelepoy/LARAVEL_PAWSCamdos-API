<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use App\Http\Requests\StorePetRequest;
use App\Http\Requests\UpdatePetRequest;
use App\Services\FileUploadService;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;


class PetController extends Controller implements HasMiddleware
{
    public function __construct(
        protected FileUploadService $fileUploadService
    ) {
        $this->fileUploadService = $fileUploadService;
    }

    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: [
                'index',
                'show',
                'getAllPetInfo',
                'petInfoCursorPaginate'
            ])
        ];
    }

    /**
     * Display a listing of the resource.
     * With pagination
     */
    public function index(Request $request)
    {
        $pets = Pet::filter($request->query('search'))
            ->paginate($request->query('limit'));

        return response()->json([
            'status' => 'success',
            'data' => $pets->items(),
            'meta' => [
                'current_page' => $pets->currentPage(),
                'last_page' => $pets->lastPage(),
                'per_page' => $pets->perPage(),
                'total' => $pets->total(),
            ],
        ]);
    }

    /**
     * Display a listing of the resource.
     * With cursor pagination
     */

    public function petInfoCursorPaginate(Request $request)
    {
        $pets = Pet::filter($request->query('search'))
            ->cursorPaginate($request->query('limit', 100)); // Default 100 per request

        return response()->json([
            'status' => 'success',
            'data' => $pets->items(),
            'meta' => [
                'per_page' => $pets->perPage(),
                'next_cursor' => $pets->nextPageUrl(),
                'prev_cursor' => $pets->previousPageUrl(),
                'has_more_pages' => $pets->hasMorePages(),
            ]
        ]);
    }

    /**
     * Display a listing of the resource.
     */

    public function getAllPetInfo(Request $request)
    {
        $pets = Pet::all();

        return response()->json([
            'status' => 'success',
            'data' => $pets
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePetRequest $request)
    {
        try {
            $data = $request->validated();

            $uploadData = $this->fileUploadService->upload($request->file('profile_image_url'));
            $data = array_merge($data, $uploadData);

            $pet = $request->user()->pets()->create($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Pet information was saved successfully',
                'data' => $pet
            ], status: 201);
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
            // Gate::authorize('scopeOwner', $pet);
            $data = $request->validated();

            $uploadData = $this->fileUploadService->upload($request->file('profile_image_url'), 'pet_image', $pet->file_path);
            $data = array_merge($data, $uploadData);

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
     * Soft delete the specified resource from storage.
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

    /**
     * Force delete the specified resource from storage.
     */
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