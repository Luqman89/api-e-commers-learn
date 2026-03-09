<?php

namespace App\Http\Requests\Api\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
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
        // Ambil ID dari route (bisa berupa ID langsung atau object model)
        $categoryId = $this->route('category');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                // Gunakan Rule::unique agar Laravel otomatis menghandle ignore ID saat update
                Rule::unique('categories', 'name')->ignore($categoryId),
            ],
            'brand' => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean'
        ];
    }
}
