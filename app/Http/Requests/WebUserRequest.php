<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WebUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
         return [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:8',
            'bio'      => 'required|string',
            'phone'    => 'required|string',
            'title'    => 'required|string',
            'content'  => 'required|string',
        ];
    }

    public function messages(): array{

        return [
            'name.required'     => 'Name is required',
            'email.required'    => 'Email is required',
            'email.unique'      => 'This email is already registered',
            'password.required' => 'Password is required',
            'password.min'      => 'Password must be at least 8 characters',
            'bio.required'      => 'Bio is required',
            'phone.required'    => 'Phone is required',
            'title.required'    => 'Post title is required',
            'content.required'  => 'Post content is required',
        ];

    }

     public function attributes(): array
    {
        return [
            'name'    => 'Full Name',
            'email'   => 'Email Address',
            'phone'   => 'Phone Number',
            'title'   => 'Post Title',
            'content' => 'Post Content',
        ];
    }
    
}
