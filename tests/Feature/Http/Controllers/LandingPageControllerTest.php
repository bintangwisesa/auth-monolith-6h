<?php

use App\Models\User;
use Modules\Products\Models\Category;
use Modules\Products\Models\Product;

it('renders a public landing page with working sign-in and registration links', function () {
    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertViewIs('landing.index')
        ->assertSee('Temukan yang')
        ->assertSee(route('login'), false)
        ->assertSee(route('register'), false)
        ->assertSee('Koleksi baru sedang disiapkan');
});

it('shows available products and their categories on the landing page', function () {
    $category = Category::factory()->create(['name' => 'Kerajinan Lokal']);
    $product = Product::factory()->create([
        'name' => 'Keranjang Anyaman',
        'price' => 85000,
        'stock' => 7,
        'is_active' => true,
    ]);
    $product->categories()->attach($category, ['added_at' => now()]);
    Product::factory()->create([
        'name' => 'Produk Tanpa Stok',
        'stock' => 0,
        'is_active' => true,
    ]);
    Product::factory()->create([
        'name' => 'Produk Nonaktif',
        'stock' => 4,
        'is_active' => false,
    ]);

    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertSee('Keranjang Anyaman')
        ->assertSee('Kerajinan Lokal')
        ->assertSee('85.000')
        ->assertDontSee('Produk Tanpa Stok')
        ->assertDontSee('Produk Nonaktif')
        ->assertViewHas('productCount', 1)
        ->assertViewHas('categoryCount', 1);
});

it('links signed-in customers to the product catalog and their orders', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('home'));

    $response->assertOk()
        ->assertSee(route('products.index'), false)
        ->assertSee(route('orders.index'), false)
        ->assertDontSee(route('register'), false);
});
