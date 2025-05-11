<?php

declare(strict_types=1);

namespace App\Http\Requests\Manage\Order;

use Illuminate\Foundation\Http\FormRequest;

class OrderStoreRequest extends FormRequest
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
            'customer' => ['required', 'string', 'min:2', 'max:255'],
            'warehouse_id' => ['required', 'integer', 'exists:warehouses,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.count' => ['required', 'integer', 'min:1'],
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
            'items.required' => 'Поле не должно быть пустым',
            'items.array' => 'Поле должно быть массивом',
            'items.min' => 'Поле не должно быть пустым',
            'items.*.product_id.required' => 'Поле не должно быть пустым',
            'items.*.product_id.integer' => 'Поле должно быть числом',
            'items.*.product_id.exists' => 'Значение не найдено',
            'items.*.count.required' => 'Поле не должно быть пустым',
            'items.*.count.integer' => 'Поле должно быть числом',
            'items.*.count.min' => 'Поле должно превышать 0',
        ];
    }

    public function withValidator($validator): void
    {
        if ($this->input('items') === null) {
            return;
        }

        $validator->after(function ($validator): void {
            $productIds = array_column($this->input('items'), 'product_id');
            $uniqueIds = array_unique($productIds);

            if (count($productIds) !== count($uniqueIds)) {
                $validator->errors()->add('items', 'Поле не должно содержать дублирующиеся значения');
            }
        });
    }
}
