<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Orders\Models\Order;
use Modules\Orders\Models\OrderStatusHistory;
use Modules\Products\Models\Product;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::query()->get();
        $products = Product::query()->get();
        $statuses = ['pending', 'processing', 'shipped', 'completed', 'cancelled'];

        for ($orderNumber = 1; $orderNumber <= 15; $orderNumber++) {
            $user = $users->random();
            $status = fake()->randomElement($statuses);
            $orderedAt = now()->subDays(fake()->numberBetween(0, 30));
            $selectedProducts = $products->random(fake()->numberBetween(1, 3));
            $items = $selectedProducts->map(function (Product $product): array {
                $quantity = fake()->numberBetween(1, 5);
                $price = (float) $product->price;

                return [
                    'product' => $product,
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => round($price * $quantity, 2),
                ];
            });

            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => sprintf('ORD-%s-%04d', now()->format('Ymd'), $orderNumber),
                'total_amount' => $items->sum('subtotal'),
                'status' => $status,
                'shipping_address' => fake()->address(),
                'ordered_at' => $orderedAt,
            ]);

            foreach ($items as $item) {
                $order->items()->create([
                    'product_id' => $item['product']->id,
                    'product_name' => $item['product']->name,
                    'price_at_purchase' => $item['price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => $status,
                'notes' => 'Status order sampel.',
                'changed_by' => $user->id,
            ]);
        }
    }
}
