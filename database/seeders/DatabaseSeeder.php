<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSizes;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 2. Definisi Permission
        $permissions = [
            'manage categories',
            'manage products',
            'checkout'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 3. Buat Role & Assign Permission
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all()); // Admin dapet semua

        $customerRole = Role::firstOrCreate(['name' => 'customer']);
        $customerRole->givePermissionTo('checkout');

        // 4. Buat User Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'], // Cek berdasarkan email
            [
                'name' => 'Syaifudin',
                'password' => bcrypt('password'),
            ]
        );
        
        // Gunakan syncRoles agar tidak double role kalau seeder dijalankan ulang
        $admin->syncRoles($adminRole);

        // 5. Buat Kategori Dummy
        // Ini akan membuat 5 kategori menggunakan CategoryFactory
        $categories = Category::factory(5)->create();

        // 6. Buat Produk Dummy yang terhubung ke Kategori
        // Kita gunakan loop agar setiap produk punya kategori dari list di atas
        foreach ($categories as $category) {
            Product::factory(3) // 3 produk per kategori
                ->has(ProductSizes::factory()->count(2), 'sizes') // Setiap produk punya 2 ukuran
                ->create([
                    'category_id' => $category->id,
                    // 'brand' => $category->brand
                ]);
        }

        // 7. Tambahkan Customer Dummy (Opsional)
        $customers = User::factory(10)->create();
        foreach ($customers as $customer) {
            $customer->syncRoles($customerRole);
        }
    }
}
