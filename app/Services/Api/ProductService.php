<?php

namespace App\Services\Api;

use App\Models\Product;
use App\Models\ProductSizes;
use App\Traits\HasFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Exception;

class ProductService
{
    use HasFile;

    public function getAll()
    {
        // Eager Load 'category' untuk menghindari N+1 Query Problem
        return Product::with(['category', 'sizes'])
            ->filter(request(['search', 'category_id', 'sort']))
            ->latest()
            ->paginate(10);
    }

    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {
            $data['is_active'] = $data['is_active'] ?? true;
            $uploadedPath = null;

            try {
                // 1. Handle Upload Gambar Utama
                if (isset($data['image'])) {
                    $uploadedPath = $this->upload_file($data['image'], 'products');
                    $data['image'] = $uploadedPath;
                }

                // 2. Buat Produk Utama
                $product = Product::create($data);

                // 3. Simpan Data Ukuran (Sizes) jika ada
                // Pastikan input di Postman berupa array 'sizes'
                if (isset($data['sizes']) && is_array($data['sizes'])) {
                    foreach ($data['sizes'] as $item) {
                        $product->sizes()->create([
                            'size'  => $item['size'],
                            'stock' => $item['stock'] ?? 0,
                        ]);
                    }
                }

                // Load relasi agar muncul di response setelah create
                return $product->load('sizes');
            } catch (Exception $e) {
                if ($uploadedPath) {
                    Storage::disk('public')->delete($uploadedPath);
                }
                throw $e;
            }
        });
    }

    public function update(Product $product, array $data)
    {
        return DB::transaction(function () use ($product, $data) {
            $oldImage = $product->image;

            if (isset($data['image'])) {
                $data['image'] = $this->upload_file($data['image'], 'products');
            }

            // 1. Update data produk utama (nama, harga, dll)
            $product->update($data);

            // 2. Update data ukuran (Sizes) jika ada dalam request
            if (isset($data['sizes']) && is_array($data['sizes'])) {
                // Hapus ukuran lama, ganti dengan yang baru (Fresh Sync)
                $product->sizes()->delete(); 
                foreach ($data['sizes'] as $item) {
                    $product->sizes()->create([
                        'size'  => $item['size'],
                        'stock' => $item['stock'] ?? 0,
                    ]);
                }
            }

            // Hapus gambar lama hanya jika ada upload gambar baru & DB sukses update
            if (isset($data['image']) && $oldImage) {
                Storage::disk('public')->delete($oldImage);
            }

            return $product->load(['category', 'sizes']);
        });
    }

    /**
     * Hapus produk dan bersihkan storage
     */
    public function delete(Product $product)
    {
        return DB::transaction(function () use ($product) {
            $imageToDelete = $product->image;
            
            // Relasi sizes akan otomatis terhapus oleh Database (Cascade)
            $product->delete();

            if ($imageToDelete) {
                Storage::disk('public')->delete($imageToDelete);
            }

            return true;
        });
    }
    
    public function updateStockPerSize(string $productSizeId, int $newStock)
    {
        $size = ProductSizes::findOrFail($productSizeId);
        $size->update(['stock' => $newStock]);
        
        return $size->load('product');
    }
}