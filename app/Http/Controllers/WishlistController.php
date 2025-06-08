<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class WishlistController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $wishlists = Wishlist::where('users_id', $userId)->get();

        return response()->json([
            'success' => true,
            'data' => $wishlists,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'destinasis_id' => 'required|integer|exists:destinasis,id',
        ]);

        $usersId = $request->user()->id;
        $destinasisId = $request->input('destinasis_id');

        $exists = Wishlist::where('users_id', $usersId)
            ->where('destinasis_id', $destinasisId)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Destinasi sudah ada di wishlist',
            ], 409);
        }

        try {
            $wishlist = Wishlist::create([
                'users_id' => $usersId,
                'destinasis_id' => $destinasisId,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan wishlist',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'data' => $wishlist,
        ], 201);
    }

    public function destroy(Request $request, int $destinasisId): JsonResponse
    {
        $userId = $request->user()->id;

        $deleted = Wishlist::where('users_id', $userId)
            ->where('destinasis_id', $destinasisId)
            ->delete();

        if ($deleted) {
            return response()->json([
                'success' => true,
                'message' => 'Wishlist berhasil dihapus',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Data wishlist tidak ditemukan',
        ], 404);
    }
}
