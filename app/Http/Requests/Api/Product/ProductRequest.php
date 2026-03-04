<?php

namespace App\Http\Requests\Api\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
            'category_id' => 'required|exists:categories,id', // Validasi krusial!
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'sizes'         => 'required|array|min:1', 
            'sizes.*.size'  => 'required|string',
            'sizes.*.stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'is_active'   => 'boolean'
        ];
    }
}
