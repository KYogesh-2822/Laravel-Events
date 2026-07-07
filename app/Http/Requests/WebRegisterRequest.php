<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WebRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
            'city' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please enter your full name.',
            'email.required' => 'Please enter your email address.',
            'email.unique' => 'This email is already registered.',
            'password.required' => 'Please enter a password.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Passwords do not match.',
            'password_confirmation.required' => 'Please confirm your password.',
            'city.required' => 'Please enter your city.',
        ];
    }
}
