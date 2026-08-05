<?php

namespace App\Services;


use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthService
{

// Fungsi Register
  public function register(array $data): array
  {
    // Validate
    $validator = Validator::make($data, [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
    ]);

    if ($validator->fails()) {
        throw new ValidationException($validator);
    }

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
}