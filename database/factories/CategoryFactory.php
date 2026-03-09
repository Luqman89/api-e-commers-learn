<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Daftar kategori yang umum untuk toko e-commerce
        $categories = ['Sneakers', 'Running Shoes', 'Formal Shoes', 'Sandals', 'Sportswear'];
        
        // Pilih satu secara random
        $name = fake()->unique()->randomElement($categories);

        return [
            'name'        => $name,
            'slug'        => Str::slug($name),
            'brand'       => fake()->randomElement(['Nike', 'Adidas', 'Puma', 'Reebok']),
            'is_active'   => true,
        ];
    }
}
