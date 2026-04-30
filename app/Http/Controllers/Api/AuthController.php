<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use App\Traits\LoggerTrait;
use App\Traits\ApiResponse;


class AuthController extends Controller
{

use LoggerTrait, ApiResponse;
    

public function register(RegisterRequest $request) : JsonResponse
    {
        $user = User::create($request->validated());

        $token = $user->createToken('auth-login')->accessToken;

        return response()->json([
            'message' => 'Registration successful!',
            'user'    => new UserResource($user),
            'token'   => $token,
        ], 201);
    }


   public function login(LoginRequest $request): JsonResponse
    {

    $this->logInfo("Login attempt: ".$request->email);
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
             $this->logError('Login failed for email: '.$request->email);
            throw ValidationException::withMessages([
                'email' => ['These credentials do not match our records.'],
            ]);
        }

        $user->tokens()->delete();

        $token = $user->createToken('auth-token')->accessToken;

        $this->logInfo("User logged in: ".$user->email);
       return $this->success([
            'user'  => new UserResource($user),
            'token' => $token,
        ], 'Login successful!');
    }


 

     public function logout(Request $request): JsonResponse
    {
        $request->user()->token()->revoke();

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }



    public function me(Request $request): jsonResponse
    {
        return response()->json([
            'user' => new UserResource($request->user())
        ]);
    }
}
