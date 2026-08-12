<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    public function __construct(
        protected AuthService $authService
    ){}

    // Register
    public function register(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $result = $this->authService->register($validatedData);

        return response()->json([
            'message' => 'Pendaftaran berhasil',
            'user' => $result['user'],
            'token' => $result['token'],
        ], 201);
    }

    // Login

    public function login(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',     
        ]);

        $result = $this->authService->login(
            $validatedData['email'],
            $validatedData['password']  
        );

        if (!$result)
        {
            return response()->json([
                'message' => 'Email atau password salah'
            ], 401);
        }

        return response()->json([
            'message' => 'Login berhasil',
            'user' => $result['user'],
            'token' => $result['token'],
        ], 200);
    }

    // Logout

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil'
        ], 200);
    }


    // Update User
    public function updateUser(Request $request): JsonResponse 
    {
        $userId = $request->user()->id;

        $validatedData = $request->validate([
           'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $userId,  
        ]);

        $result = $this->authService->updateProfile(
            $userId,
            $validatedData
        );

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => $result   
        ], 200);
    }
}
