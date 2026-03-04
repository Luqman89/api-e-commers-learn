<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use Sluggable, HasUlids, HasFactory;

    protected $fillable = [
        'category_id', 'name', 'slug', 'description', 
        'price', 'image', 'is_active'
    ];

    public function sluggable(): array
    {
        return [
            'slug' => ['source' => 'name', 'onUpdate' => true]
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function sizes()
    {
        return $this->hasMany(ProductSizes::class);
    }

    public function scopeFilter($query, array $filters)
{
    // Filter berdasarkan pencarian nama
    $query->when($filters['search'] ?? false, function ($query, $search) {
        $query->where('name', 'like', '%' . $search . '%');
    });

    // Filter berdasarkan category_id (ULID)
    $query->when($filters['category_id'] ?? false, function ($query, $category_id) {
        $query->where('category_id', $category_id);
    });

    // Urutkan berdasarkan harga (asc/desc)
    $query->when($filters['sort'] ?? false, function ($query, $sort) {
        if ($sort === 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($sort === 'price_desc') {
            $query->orderBy('price', 'desc');
        }
    });
}
}
