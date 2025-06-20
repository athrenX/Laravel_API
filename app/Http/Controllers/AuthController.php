<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'nama' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Buat user baru
            $user = User::create([
                'nama' => $request->input('nama'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
                'role' => 'user', // Default role
            ]);

            // Buat token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'nama' => $user->nama,
                        'email' => $user->email,
                        'foto_profil' => null,
                        'payment_method' => null,
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Login user
     */
    public function login(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $email = $request->input('email');
        $password = $request->input('password');

        // Cari user dulu berdasarkan email
        $user = User::where('email', $email)->first();

        if (!$user) {
            Log::error("Login gagal: user dengan email $email tidak ditemukan");
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials - user not found'
            ], 401);
        }

        // Cek password secara manual pakai Hash::check
        if (!Hash::check($password, $user->password)) {
            Log::error("Login gagal: password salah untuk user $email");
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials - wrong password'
            ], 401);
        }

        // Kalau lolos, lakukan Auth::attempt (sebenarnya optional karena sudah dicek manual)
        if (!Auth::attempt(['email' => $email, 'password' => $password])) {
            Log::error("Login gagal: Auth::attempt gagal untuk user $email");
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Create token dan response berhasil login
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'nama' => $user->nama,
                    'email' => $user->email,
                    'foto_profil' => $user->foto_profil ? url('storage/' . $user->foto_profil) : null,
                    'payment_method' => $user->payment_method,
                    'role' => $user->role,
                ],
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ]);
    } catch (\Exception $e) {
        Log::error("Login failed with exception: " . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Login failed',
            'error' => $e->getMessage()
        ], 500);
    }
}


    /**
     * Get authenticated user
     */
    public function getUser(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'nama' => $user->nama,
                    'email' => $user->email,
                    'foto_profil' => $user->foto_profil
                        ? url('storage/' . $user->foto_profil)
                        : null,
                    'payment_method' => $user->payment_method,
                    'role' => $user->role,  // Tambahkan ini
                ]
            ]
        ]);

    }


    /**
     * Update user profile
     */
 public function updateProfile(Request $request)
{
    try {
        $user = $request->user();

        Log::info('[PROFILE] SEBELUM UPDATE:', [
            'id' => $user->id,
            'foto_profil' => $user->foto_profil
        ]);

        $validator = Validator::make($request->all(), [
            'nama' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            'foto_profil' => 'sometimes|file|image|mimes:jpg,jpeg,png|max:2048',
            'payment_method' => 'sometimes|in:Bank Transfer,E-Wallet,Kartu Kredit'
        ]);

        if ($validator->fails()) {
            Log::warning('[PROFILE] Validasi gagal', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->has('nama')) {
            $user->nama = $request->nama;
        }
        if ($request->has('email')) {
            $user->email = $request->email;
        }
        if ($request->has('payment_method')) {
            $user->payment_method = $request->payment_method;
        }

        // LOG sebelum update foto
        Log::info('[PROFILE] Sebelum update foto:', [
            'foto_profil_lama' => $user->foto_profil
        ]);

        if ($request->hasFile('foto_profil')) {
            Log::info('[PROFILE] Ada upload foto:', [
                'file_name' => $request->file('foto_profil')->getClientOriginalName()
            ]);
            if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
                Log::info('[PROFILE] Hapus foto lama:', [
                    'file_path' => $user->foto_profil
                ]);
                Storage::disk('public')->delete($user->foto_profil);
            }
            $path = $request->file('foto_profil')->store('foto_profil', 'public');
            Log::info('[PROFILE] Path hasil upload:', [
                'new_path' => $path
            ]);
            $user->foto_profil = $path;
        }

        // LOG setelah proses update foto
        Log::info('[PROFILE] SEBELUM SAVE:', [
            'foto_profil' => $user->foto_profil
        ]);

        $user->save();

        Log::info('[PROFILE] SETELAH SAVE:', [
            'foto_profil' => $user->foto_profil
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'nama' => $user->nama,
                    'email' => $user->email,
                    'foto_profil' => $user->foto_profil
                        ? url('storage/' . $user->foto_profil)
                        : null,
                    'payment_method' => $user->payment_method,
                ]
            ]
        ]);
    } catch (\Exception $e) {
        Log::error('[PROFILE] ERROR:', ['msg' => $e->getMessage()]);
        return response()->json([
            'success' => false,
            'message' => 'Update profile failed',
            'error' => $e->getMessage()
        ], 500);
    }
}





    /**
     * Change password
     */
    // UserController.php
public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:6|max:255|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json(['message' => 'Password lama salah'], 400);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'Password berhasil diubah']);
    }


    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
