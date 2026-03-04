<?php

namespace App\Http\Requests\Api\Cart;

use Illuminate\Foundation\Http\FormRequest;

class CartRequest extends FormRequest
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
            'product_size_id' => 'required|exists:product_sizes,id',
            'quantity'        => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'product_size_id.exists' => 'Ukuran produk tidak ditemukan.',
            'quantity.min'           => 'Minimal pembelian adalah 1 pcs.',
        ];
    }
}
