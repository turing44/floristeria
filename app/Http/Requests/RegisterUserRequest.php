<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
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
            "name" => "required|string|max:255",
            "email" => "required|string|email|max:255",
            "password" => "required",
            "rol" => "required|in:experto,admin,anonimo"
        ];
    }

    public function messages()
    {
        return [
            "email.email" => "No tiene formato de email",
            "rol.in" => "No es un rol permitido",
            "name.required" => "El nombre es requerido"
        ];
    }

}
