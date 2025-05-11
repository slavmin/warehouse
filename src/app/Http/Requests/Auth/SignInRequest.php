<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class SignInRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return ! auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:100'],
            'password' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Не указан email',
            'email.email' => 'Недействительный email',
            'email.max' => 'Email не должен превышать 100 символов',
            'password.required' => 'Не указан пароль',
            'password.min' => 'Пароль должен превышать 3 символа',
            'password.max' => 'Пароль не должен превышать 255 символов',
        ];
    }
}
