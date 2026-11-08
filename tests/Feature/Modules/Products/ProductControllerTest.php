<?php

use App\Models\User;
use Modules\Products\Models\Category;
use Modules\Products\Models\Product;

it('renders the edit form with the product and its categories', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create([
        'name' => 'Kopi Nusantara',
        'slug' => 'kopi-nusantara',
        'description' => 'Kopi pilihan.',
    ]);
    $category = Category::factory()->create(['name' => 'Minuman']);
    $product->categories()->attach($category, ['added_at' => now()]);

    $response = $this->actingAs($user)->get(route('products.edit', $product));

    $response->assertOk()
        ->assertViewIs('products.edit')
        ->assertSee('Kopi Nusantara')
        ->assertSee('kopi-nusantara')
        ->assertSee('Kopi pilihan.')
        ->assertSee('Minuman')
        ->assertSee('name="low_stock_threshold"', false)
        ->assertSee('name="is_active"', false);
});

it('links each product row to its edit form', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create();

    $response = $this->actingAs($user)->get(route('products.index'));

    $response->assertOk()
        ->assertSee('href="'.route('products.index').'"', false)
        ->assertSee(route('products.edit', $product), false);
});

it('redirects guests to login when they open the product edit form', function () {
    $product = Product::factory()->create();

    $response = $this->get(route('products.edit', $product));

    $response->assertRedirect(route('login'));
});

it('updates product details and replaces its categories', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create([
        'name' => 'Nama Lama',
        'slug' => 'nama-lama',
    ]);
    $oldCategory = Category::factory()->create();
    $newCategory = Category::factory()->create();
    $product->categories()->attach($oldCategory, ['added_at' => now()]);

    $response = $this->actingAs($user)->put(route('products.update', $product), [
        'name' => 'Kopi Baru',
        'slug' => 'nama-lama',
        'description' => 'Deskripsi baru.',
        'price' => '1299.50',
        'stock' => 24,
        'low_stock_threshold' => 4,
        'is_active' => 0,
        'sync_categories' => 1,
        'category_ids' => [$newCategory->id],
    ]);

    $response->assertRedirect(route('products.index', absolute: false))
        ->assertSessionHas('success', 'Produk berhasil diperbarui.');

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => 'Kopi Baru',
        'slug' => 'nama-lama',
        'description' => 'Deskripsi baru.',
        'price' => '1299.50',
        'stock' => 24,
        'low_stock_threshold' => 4,
        'is_active' => 0,
    ]);
    $this->assertDatabaseHas('category_product', [
        'product_id' => $product->id,
        'category_id' => $newCategory->id,
    ]);
    $this->assertDatabaseMissing('category_product', [
        'product_id' => $product->id,
        'category_id' => $oldCategory->id,
    ]);
});

it('keeps the product unchanged when its slug is already in use', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create([
        'name' => 'Produk Asli',
        'slug' => 'produk-asli',
    ]);
    Product::factory()->create(['slug' => 'slug-dipakai']);

    $response = $this->actingAs($user)->put(route('products.update', $product), [
        'name' => 'Nama yang Tidak Disimpan',
        'slug' => 'slug-dipakai',
        'price' => 100,
        'stock' => 10,
        'low_stock_threshold' => 2,
        'is_active' => 1,
    ]);

    $response->assertSessionHasErrors(['slug' => 'Slug produk sudah digunakan.']);
    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => 'Produk Asli',
        'slug' => 'produk-asli',
    ]);
});
