<?php

namespace App\Services\Api\Category;

use App\Models\Category;
use Exception;
use Illuminate\Support\Facades\DB;

class CategoryService
{
    public function getAll()
    {
        return Category::latest()->get();
    }

    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {
            $data['is_active'] = $data['is_active'] ?? true;
            
            return Category::create($data);
        });
    }

    public function update(Category $category, array $data)
    {
        return DB::transaction(function () use ($category, $data) {
            if ($category->update($data)) {
                return $category;
            }
            
            throw new Exception("Update Gagal");
        });
    }

    public function delete(Category $category)
    {
        return DB::transaction(function () use ($category) {
            // Langsung hapus saja, tidak perlu cek imageToDelete
            return $category->delete();
        });
    }
}