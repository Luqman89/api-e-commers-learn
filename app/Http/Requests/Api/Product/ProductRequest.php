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
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch') || $this->has('_method');

        return [
            'category_id' => [
                $isUpdate ? 'sometimes' : 'required',
                'exists:categories,id'
            ],
            'name' => [
                $isUpdate ? 'sometimes' : 'required',
                'string',
                'max:255'
            ],
            'price' => [
                $isUpdate ? 'sometimes' : 'required',
                'numeric',
                'min:0'
            ],
            'sizes' => [
                $isUpdate ? 'sometimes' : 'required',
                'array',
                'min:1'
            ],
            'sizes.*.size' => 'required_with:sizes|string',
            'sizes.*.stock' => 'required_with:sizes|integer|min:0',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'is_active'   => 'boolean'
        ];
    }
}
