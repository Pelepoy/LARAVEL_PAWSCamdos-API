<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // Admin dashboar summary
    public function dashboard()
    {
        $counts = User::getDashboardCounts();

        return response()->json([
            'total_users' => $counts->total_users,
            'total_pets' => $counts->total_pets,
        ]);
    }

    // Get all users
    public function getAllUsers(Request $request)
    {
        $users = User::with('pets')
            ->filter($request->query('search'))
            ->paginate($request->query('limit', 10));

        return response()->json([
            'status' => 'success',
            'data' => $users->items(),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ]
        ]);

    }

    // Get all pets
    public function getAllPets(Request $request)
    {
        $pets = Pet::with('user')
            ->filter($request->query('search'))
            ->paginate($request->query('limit', 10));


        return response()->json([
            'status' => 'success',
            'data' => $pets->items(),
            'meta' => [
                'current_page' => $pets->currentPage(),
                'last_page' => $pets->lastPage(),
                'per_page' => $pets->perPage(),
                'total' => $pets->total(),
            ]
        ]);
    }

    // Delete a specific user by ID
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'User deleted successfully'], 200);
    }

    // Delete a specific pet by ID
    public function softDeletePet($id)
    {
        $pet = Pet::findOrFail($id);
        $pet->delete();
        return response()->json(['message' => 'Pet info deleted successfully'], 200);
    }

    public function restorePetInfo($id)
    {
        $pet = Pet::withTrashed()->where('id', $id)->first();
        $pet->restore();
        return response()->json(['message' => 'Pet info restored successfully'], 200);
    }

    public function forceDeletePet($id)
    {
        $pet = Pet::withTrashed()->where('id', $id)->first();
        $pet->forceDelete();
        return response()->json(['message' => 'Pet info force deleted successfully'], 200);
    }


}