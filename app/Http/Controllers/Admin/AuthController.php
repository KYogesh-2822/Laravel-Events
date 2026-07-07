<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showAdminLogin(): View
    {
        return view('auth.admin-login');
    }

    public function adminLogin(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended('/admin/dashboard')->with('success', 'You are now logged in as admin.');
        }

        Auth::guard('admin')->logout();

        return back()->withErrors([
            'email' => 'Admin credentials are invalid.',
        ])->onlyInput('email');
    }

    public function adminLogout(): RedirectResponse
    {
        Auth::guard('admin')->logout();
        Session::invalidate();
        Session::regenerateToken();

        return Redirect::route('admin.admin.login');
    }
}
