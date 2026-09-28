<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Contracts\Validation\Validator;

class RegisterUserRequest extends FormRequest
{
    public function authorize(): bool 
    { 
        return true; 
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:100', 'unique:users,name'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => [
                'required', 
                'confirmed',
                'regex:/^\S*$/', 
                Password::min(8)->letters()->mixedCase()->numbers(),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'Este nombre de usuario ya está en uso. Por favor, elige uno diferente.',
            'password.regex' => 'La contraseña no puede contener espacios en blanco.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        session()->flashInput($this->except(['name', 'password', 'password_confirmation']));
        
        parent::failedValidation($validator);
    }
}