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
use Illuminate\Foundation\Inspiring;


class AuthController extends Controller
{

use LoggerTrait, ApiResponse;
    

    public function register(RegisterRequest $request) : JsonResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        $token = $user->createToken('auth-login')->accessToken;
        $quote = config('quotes')[array_rand(config('quotes'))];
        $inbuildQuote = $this->cleanInspiringQuote(Inspiring::quote());

        // Attach quotes to the user model so the resource can access them
        $user->quote = $quote;
        $user->inbuildQuote = $inbuildQuote;

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
        $quote = collect(config('quotes'))->random();
        $inbuildQuote = Inspiring::quote();

        $user->quote = $quote;
        $user->inbuildQuote = $inbuildQuote;

        $this->logInfo("User logged in: ".$user->email);
       return $this->success([
            'user'  => new UserResource($user),
            'token' => $token,
        ], 'Login successful!');
    }


 

     public function logout(Request $request): JsonResponse
    {
        $request->user()->token()->revoke();
        $request->user()->token()->delete();

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


     private function cleanInspiringQuote($quote): string
    {
        // Remove Laravel/Symfony console tags
        $quote = strip_tags($quote);
        // Remove new lines and extra spaces
        $quote = preg_replace('/\s+/', ' ', $quote);
        return trim($quote);
    }
}
