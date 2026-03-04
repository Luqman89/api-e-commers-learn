<?php

namespace App\Http\Controllers\Api\Category;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Category\CategoryRequest;
use App\Http\Resources\Api\Category\CategoryResource;
use App\Models\Category;
use App\Services\Api\Category\CategoryService as CategoryCategoryService;
use Exception;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryCategoryService $service)
    {
        $this->categoryService = $service;
    }

    public function index()
    {
        $categories = $this->categoryService->getAll();
        return ApiResponse::success('Data Kategori Berhasil Diambil', CategoryResource::collection($categories));
    }

    public function store(CategoryRequest $request)
    {
        try {
            $category = $this->categoryService->store($request->validated());
            return ApiResponse::success('Kategori Berhasil Dibuat', new CategoryResource($category));
        } catch (Exception $e) {
            return ApiResponse::error('Gagal membuat kategori: ' . $e->getMessage(), 500);
        }
    }

    public function show(Category $category)
    {
         $category->load('products');
        return ApiResponse::success('Detail Kategori', new CategoryResource($category));
    }

    public function update(CategoryRequest $request, Category $category)
    {
        try {
            $updated = $this->categoryService->update($category, $request->validated());
            return ApiResponse::success('Kategori Berhasil Diperbarui', new CategoryResource($updated));
        } catch (Exception $e) {
            return ApiResponse::error('Gagal memperbarui kategori', 500);
        }
    }

    public function destroy(Category $category)
    {
        try {
            $this->categoryService->delete($category);
            return ApiResponse::success('Kategori Berhasil Dihapus');
        } catch (Exception $e) {
            return ApiResponse::error('Gagal menghapus kategori', 500);
        }
    }
}
