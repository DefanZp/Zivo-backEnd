<?php

namespace App\Services;


use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{

// Fungsi Register
  public function register(array $data): array
  {
    
    $user = User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => $data['password'],
        'role' => 'customer',
    ]);

    $token = $user->createToken('auth_token')->plainTextToken;

    return [
        'user' => $user,
        'token' => $token,
    ];
  }
    
    // Fungsi Login
    public function login(string $email, string $password): ?array
    {
        $user = User::where('email', $email)->first();

        // validasi kecocokan email dan password
        if (!$user || !Hash::check($password, $user->password)) 
        {
            return null;
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    // Fungsi edit user data
    public function updateProfile(int $userId, array $data)
    {
        $user = User::findOrFail($userId);

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        return $user->fresh();
    }
}