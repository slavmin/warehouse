<?php

declare(strict_types=1);

namespace App\Http\Requests\Manage\Stock;

use App\Enums\StockOperations;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StockChangeIndexRequest extends FormRequest
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
            'warehouse_id' => ['nullable', 'integer', 'exists:warehouses,id'],
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'operation' => ['nullable', 'string', Rule::in(StockOperations::valuesList())],
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
            'warehouse_id.required' => 'Поле не должно быть пустым',
            'warehouse_id.integer' => 'Поле должно быть числом',
            'warehouse_id.exists' => 'Значение не найдено',
            'product_id.required' => 'Поле не должно быть пустым',
            'product_id.integer' => 'Поле должно быть числом',
            'product_id.exists' => 'Значение не найдено',
            'date_from.date' => 'Поле должно быть датой',
            'date_to.date' => 'Поле должно быть датой',
            'date_to.after_or_equal' => 'Неверное значение даты',
            'operation.string' => 'Поле должно быть строкой',
            'operation.in' => 'Значение не найдено',
            'per_page.integer' => 'Поле должно быть числом',
            'page.integer' => 'Поле должно быть числом',
        ];
    }
}
