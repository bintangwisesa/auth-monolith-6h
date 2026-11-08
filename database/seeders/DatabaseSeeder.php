<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Auth\Models\Role;
use Modules\Products\Models\Category;
use Modules\Products\Models\Product;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed roles
        $adminRole = Role::create(['name' => 'Administrator', 'slug' => 'admin']);
        $userRole = Role::create(['name' => 'User', 'slug' => 'user']);

        // 2. Seed admin user (Cukup satu kali pembuatan user)
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
        ]);

        // 3. Hubungkan Admin dengan Role menggunakan Eloquent attach()
        $admin->roles()->attach($adminRole);

        // Buat pengguna sampel dan hubungkan dengan role User.
        User::factory(5)->create()->each(function (User $user) use ($userRole): void {
            $user->roles()->attach($userRole);
        });

        // 4. Seed categories
        $categories = Category::factory()->count(10)->create();

        // 5. Seed products
        $products = Product::factory()->count(50)->create();

        // 6. Attach categories to products
        $products->each(function ($product) use ($categories) {
            $product->categories()->attach(
                $categories->random(rand(1, 3))->pluck('id')->toArray(),
                ['added_at' => now()]
            );
        });

        $this->call(OrderSeeder::class);
    }
}
