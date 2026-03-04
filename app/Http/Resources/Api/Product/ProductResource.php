<?php

namespace App\Http\Resources\Api\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'slug'        => $this->slug,
            'category'    => [
                'id'   => $this->category_id,
                'name' => $this->category->name, // Mengambil dari relasi belongsTo
            ],
            'sizes' => $this->sizes->map(function($item) {
                return [
                    'id'    => $item->id,
                    'size'  => $item->size,
                    'stock' => $item->stock,
                ];
            }),
            'price'       => (float) $this->price,
            'image'       => $this->image ? asset('storage/' . $this->image) : null,
            'description' => $this->description,
            'is_active'   => (bool) $this->is_active,
            'created_at'  => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
