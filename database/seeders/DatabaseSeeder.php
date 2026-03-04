<?php

namespace Database\Seeders;

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
    }
}
