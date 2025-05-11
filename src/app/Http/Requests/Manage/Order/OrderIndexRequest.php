<?php

declare(strict_types=1);

namespace App\Http\Requests\Manage\Order;

use App\Enums\OrderStatuses;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderIndexRequest extends FormRequest
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
            'customer' => ['nullable', 'string', 'min:2', 'max:255'],
            'warehouse_id' => ['nullable', 'integer', 'exists:warehouses,id'],
            'status' => ['nullable', Rule::in(OrderStatuses::valuesList())],
            'per_page' => ['nullable', 'integer', 'min:10', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'customer.required' => 'Поле не должно быть пустым',
            'customer.min' => 'Поле должно превышать 1 символ',
            'customer.max' => 'Поле не должно превышать 255 символов',
            'warehouse_id.required' => 'Поле не должно быть пустым',
            'warehouse_id.integer' => 'Поле должно быть числом',
            'warehouse_id.exists' => 'Значение не найдено',
            'status.in' => 'Значение не найдено',
            'per_page.integer' => 'Поле должно быть числом',
            'page.integer' => 'Поле должно быть числом',
        ];
    }
}
