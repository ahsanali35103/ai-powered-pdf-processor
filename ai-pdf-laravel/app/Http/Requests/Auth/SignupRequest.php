<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class SignupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'             => ['required', 'string', 'max:255'],
            'email'            => ['required', 'email'],
            'password'         => ['required', 'string', 'min:8', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'],
            'confirm_password' => ['required', 'same:password'],
            'organization'     => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'             => 'Name is required.',
            'email.required'            => 'Email is required.',
            'email.email'               => 'Please provide a valid email address.',
            'password.required'         => 'Password is required.',
            'password.min'              => 'Password must be at least 8 characters.',
            'password.regex'            => 'Password must contain at least 1 lowercase letter, 1 uppercase letter, and 1 number.',
            'confirm_password.required' => 'Please confirm your password.',
            'confirm_password.same'     => 'Passwords do not match.',
            'organization.required'     => 'Organization is required.',
        ];
    }
}