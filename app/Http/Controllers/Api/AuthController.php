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
        $result = $this->authService->register($request->all());

        return response()->json([
            'message' => 'Pendaftaran berhasil',
            'user' => $result['user'],
            'token' => $result['token'],
        ], 201);
    }

    // Login

    public function login(Request $request): JsonResponse
    {
        $result = $this->authService->login(
            $request->email,
            $request->password
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
        $result = $this->authService->updateProfile(
            $request->user()->id,
            $request->all()
        );

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => $result   
        ]);
    }
}
