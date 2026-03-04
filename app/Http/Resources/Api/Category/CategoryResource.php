<?php

namespace App\Http\Resources\Api\Category;

use App\Http\Resources\Api\Product\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'slug'      => $this->slug,
            'brand'     => $this->brand,
            'products'  => ProductResource::collection($this->whenLoaded('products')),
            'is_active' => $this->is_active,
            'created_at'=> $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
