<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\WebRegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class WebAuthController extends Controller
{
    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(WebRegisterRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($request->password);

        $user = User::create($data);

        Auth::login($user);

        return redirect()->route('home')->with('success', 'Registration successful. You are now logged in.');
    }

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('home'))->with('success', 'You are now logged in.');
        }

        return back()->withErrors([
            'email' => 'These credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(): RedirectResponse
    {
        Auth::logout();
        Session::invalidate();
        Session::regenerateToken();

        return Redirect::to('/')->with('success', 'You have been logged out.');
    }

}
