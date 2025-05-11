<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class SignUpRequest extends FormRequest
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
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'max:40', 'confirmed'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Не указано имя',
            'name.min' => 'Имя должно превышать 3 символа',
            'name.max' => 'Имя не должно превышать 255 символов',
            'email.required' => 'Не указан email',
            'email.email' => 'Недействительный email',
            'email.max' => 'Email не должен превышать 100 символов',
            'email.unique' => 'Недействительный email',
            'password.required' => 'Не указан пароль',
            'password.min' => 'Пароль должен превышать 3 символа',
            'password.max' => 'Пароль не должен превышать 40 символов',
            'password.confirmed' => 'Подтвердите пароль',
            'password_confirmation.required' => 'Пароли не совпадают',
        ];
    }
}
