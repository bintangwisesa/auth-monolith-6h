<?php

use App\Models\User;
use Modules\Auth\Models\Role;
use Modules\Orders\Models\Order;
use Modules\Orders\Models\OrderItem;
use Modules\Products\Models\Product;

it('requires authentication to browse orders', function () {
    $response = $this->get(route('orders.index'));

    $response->assertRedirect(route('login'));
});

it('shows only the signed-in customer’s orders', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $ownOrder = Order::create([
        'user_id' => $user->id,
        'order_number' => 'ORD-OWN-001',
        'total_amount' => 100,
        'status' => 'pending',
        'ordered_at' => now(),
    ]);
    Order::create([
        'user_id' => $otherUser->id,
        'order_number' => 'ORD-OTHER-001',
        'total_amount' => 200,
        'status' => 'pending',
        'ordered_at' => now(),
    ]);

    $response = $this->actingAs($user)->get(route('orders.index'));

    $response->assertOk()
        ->assertSee($ownOrder->order_number)
        ->assertDontSee('ORD-OTHER-001')
        ->assertSee(route('orders.create'), false);
});

it('lets an administrator see every customer order', function () {
    $admin = User::factory()->create();
    $role = Role::create(['name' => 'Administrator', 'slug' => 'admin']);
    $admin->roles()->attach($role);
    $customer = User::factory()->create();
    $order = Order::create([
        'user_id' => $customer->id,
        'order_number' => 'ORD-ADMIN-VIEW-001',
        'total_amount' => 100,
        'status' => 'pending',
        'ordered_at' => now(),
    ]);

    $response = $this->actingAs($admin)->get(route('orders.index'));

    $response->assertOk()
        ->assertSee($order->order_number)
        ->assertSee($customer->email);
});

it('shows only active products with stock on the order form', function () {
    $user = User::factory()->create();
    Product::factory()->create([
        'name' => 'Produk Siap Pesan',
        'stock' => 4,
        'is_active' => true,
    ]);
    Product::factory()->create([
        'name' => 'Produk Habis',
        'stock' => 0,
        'is_active' => true,
    ]);
    Product::factory()->create([
        'name' => 'Produk Nonaktif',
        'stock' => 4,
        'is_active' => false,
    ]);

    $response = $this->actingAs($user)->get(route('orders.create'));

    $response->assertOk()
        ->assertViewIs('orders.create')
        ->assertSee('Produk Siap Pesan')
        ->assertDontSee('Produk Habis')
        ->assertDontSee('Produk Nonaktif');
});

it('creates an order, reduces stock, and records the initial status', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create([
        'name' => 'Kopi Nusantara',
        'price' => 1250,
        'stock' => 8,
        'is_active' => true,
    ]);

    $response = $this->actingAs($user)->post(route('orders.store'), [
        'items' => [
            ['product_id' => $product->id, 'quantity' => 2],
        ],
        'shipping_address' => 'Jl. Merdeka No. 10, Bandung',
    ]);

    $response->assertRedirect(route('orders.show', Order::query()->first(), absolute: false))
        ->assertSessionHas('success', 'Pesanan berhasil dibuat.');

    $order = Order::query()->firstOrFail();
    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'user_id' => $user->id,
        'total_amount' => '2500.00',
        'status' => 'pending',
        'shipping_address' => 'Jl. Merdeka No. 10, Bandung',
    ]);
    $this->assertDatabaseHas('order_items', [
        'order_id' => $order->id,
        'product_id' => $product->id,
        'product_name' => 'Kopi Nusantara',
        'quantity' => 2,
        'subtotal' => '2500.00',
    ]);
    $this->assertDatabaseHas('order_status_histories', [
        'order_id' => $order->id,
        'status' => 'pending',
        'changed_by' => $user->id,
    ]);
    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'stock' => 6,
    ]);
});

it('rejects an order that exceeds available stock without creating records', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create([
        'stock' => 1,
        'is_active' => true,
    ]);

    $response = $this->actingAs($user)->from(route('orders.create'))->post(route('orders.store'), [
        'items' => [
            ['product_id' => $product->id, 'quantity' => 2],
        ],
        'shipping_address' => 'Jl. Merdeka No. 10, Bandung',
    ]);

    $response->assertRedirect(route('orders.create', absolute: false))
        ->assertSessionHasErrors('stock');
    $this->assertDatabaseCount('orders', 0);
    $this->assertDatabaseCount('order_items', 0);
    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'stock' => 1,
    ]);
});

it('does not allow a customer to view another customer’s order', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $order = Order::create([
        'user_id' => $otherUser->id,
        'order_number' => 'ORD-PRIVATE-001',
        'total_amount' => 100,
        'status' => 'pending',
        'ordered_at' => now(),
    ]);

    $response = $this->actingAs($user)->get(route('orders.show', $order));

    $response->assertNotFound();
});

it('lets an administrator cancel an order and restore its stock', function () {
    $admin = User::factory()->create();
    $role = Role::create(['name' => 'Administrator', 'slug' => 'admin']);
    $admin->roles()->attach($role);
    $customer = User::factory()->create();
    $product = Product::factory()->create(['stock' => 3]);
    $order = Order::create([
        'user_id' => $customer->id,
        'order_number' => 'ORD-CANCEL-001',
        'total_amount' => 200,
        'status' => 'pending',
        'ordered_at' => now(),
    ]);
    OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'product_name' => $product->name,
        'price_at_purchase' => 100,
        'quantity' => 2,
        'subtotal' => 200,
    ]);
    $product->decrement('stock', 2);

    $response = $this->actingAs($admin)->patch(route('orders.status.update', $order), [
        'status' => 'cancelled',
        'notes' => 'Pembatalan oleh administrator.',
    ]);

    $response->assertRedirect(route('orders.show', $order, absolute: false))
        ->assertSessionHas('success', 'Status pesanan berhasil diperbarui.');
    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'status' => 'cancelled',
    ]);
    $this->assertDatabaseHas('order_status_histories', [
        'order_id' => $order->id,
        'status' => 'cancelled',
        'notes' => 'Pembatalan oleh administrator.',
        'changed_by' => $admin->id,
    ]);
    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'stock' => 3,
    ]);
});

it('forbids customers from changing order status', function () {
    $customer = User::factory()->create();
    $order = Order::create([
        'user_id' => $customer->id,
        'order_number' => 'ORD-STATUS-001',
        'total_amount' => 100,
        'status' => 'pending',
        'ordered_at' => now(),
    ]);

    $response = $this->actingAs($customer)->patch(route('orders.status.update', $order), [
        'status' => 'processing',
    ]);

    $response->assertForbidden();
    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'status' => 'pending',
    ]);
    $this->assertDatabaseCount('order_status_histories', 0);
});

it('rejects order status transitions that skip a workflow step', function () {
    $admin = User::factory()->create();
    $role = Role::create(['name' => 'Administrator', 'slug' => 'admin']);
    $admin->roles()->attach($role);
    $customer = User::factory()->create();
    $order = Order::create([
        'user_id' => $customer->id,
        'order_number' => 'ORD-TRANSITION-001',
        'total_amount' => 100,
        'status' => 'pending',
        'ordered_at' => now(),
    ]);

    $response = $this->actingAs($admin)->patch(route('orders.status.update', $order), [
        'status' => 'completed',
    ]);

    $response->assertSessionHasErrors('status');
    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'status' => 'pending',
    ]);
    $this->assertDatabaseCount('order_status_histories', 0);
});
