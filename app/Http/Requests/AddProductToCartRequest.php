<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddProductToCartRequest extends FormRequest
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
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1|max:20',//
        ];
    }

    public function messages()
    {
        return [
            'product_id.required' => 'Необходимо указать ID продукта.',
            'product_id.exists' => 'Продукт с таким ID не существует.',
            'quantity.integer' => 'Количество должно быть целым числом.',
            'quantity.min' => 'Минимальное количество - 1.',
            'quantity.max' => 'Максимальное количество - 20.',
        ];
    }
    
}
