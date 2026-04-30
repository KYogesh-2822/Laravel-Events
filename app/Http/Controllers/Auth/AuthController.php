<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\WebUserRequest;
use App\Services\AuthService;

class AuthController extends Controller
{

  public function __construct(protected AuthService $authService){}

  public function register(WebUserRequest $request){

    $user  =  $this->authService->register($request);

  }
}




















// app/Http/Controllers/AuthController.php

namespace App\Http\Controllers;

use App\Services\AuthService;
use App\Http\Requests\RegisterRequest;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request)
    {
        // No need to validate here
        // RegisterRequest handles all validation automatically
        // If validation fails it never reaches here

        try {
            $user = $this->authService->register($request);

            return response()->json([
                'message' => 'Registration Successful',
                'user'    => $user
            ], 201);

        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Registration Failed',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}