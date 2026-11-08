<?php

namespace Modules\Orders\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Modules\Orders\Enums\OrderStatus;
use Modules\Orders\Events\OrderCreated;
use Modules\Orders\Events\OrderStatusChanged;
use Modules\Orders\Models\Order;
use Modules\Orders\Models\OrderItem;
use Modules\Orders\Models\OrderStatusHistory;
use Modules\Products\Exceptions\InsufficientStockException;
use Modules\Shared\Contracts\ProductServiceInterface;
use RuntimeException;

class OrderService
{
    public function __construct(
        private ProductServiceInterface $productService,
    ) {}

    /**
     * @param  list<array{product_id: int, quantity: int}>  $items
     */
    public function createOrder(int $userId, array $items, ?string $shippingAddress = null): Order
    {
        $order = DB::transaction(function () use ($userId, $items, $shippingAddress): Order {
            $order = Order::create([
                'user_id' => $userId,
                'order_number' => 'ORD-'.Str::upper((string) Str::ulid()),
                'total_amount' => 0,
                'status' => OrderStatus::Pending->value,
                'shipping_address' => $shippingAddress,
                'ordered_at' => now(),
            ]);

            $totalAmount = 0.0;

            foreach ($items as $item) {
                $product = $this->productService->getProductForOrder((int) $item['product_id']);

                if (! $product || ! $product['is_active']) {
                    throw ValidationException::withMessages([
                        'items' => 'Salah satu produk sudah tidak tersedia.',
                    ]);
                }

                $quantity = (int) $item['quantity'];

                if (! $this->productService->isStockAvailable($product['id'], $quantity)) {
                    throw new InsufficientStockException(
                        $product['name'],
                        $product['stock'],
                        $quantity,
                    );
                }

                $subtotal = round($product['price'] * $quantity, 2);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product['id'],
                    'product_name' => $product['name'],
                    'price_at_purchase' => $product['price'],
                    'quantity' => $quantity,
                    'subtotal' => $subtotal,
                ]);

                if (! $this->productService->reduceStock($product['id'], $quantity)) {
                    $currentProduct = $this->productService->getProductForOrder($product['id']) ?? $product;

                    throw new InsufficientStockException(
                        $currentProduct['name'],
                        $currentProduct['stock'],
                        $quantity,
                    );
                }

                $totalAmount += $subtotal;
            }

            $order->update(['total_amount' => $totalAmount]);

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => OrderStatus::Pending->value,
                'notes' => 'Pemesanan dibuat.',
                'changed_by' => $userId,
            ]);

            return $order->load('items', 'statusHistories');
        });

        event(new OrderCreated($order->load('items')));

        return $order;
    }

    public function updateStatus(Order $order, string $newStatus, int $changedBy, ?string $notes = null): Order
    {
        $nextStatus = OrderStatus::tryFrom($newStatus);

        if (! $nextStatus) {
            throw ValidationException::withMessages([
                'status' => 'Status pesanan tidak valid.',
            ]);
        }

        [$updatedOrder, $oldStatus] = DB::transaction(function () use ($order, $nextStatus, $changedBy, $notes): array {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            $currentStatus = OrderStatus::tryFrom($lockedOrder->status);

            if (! $currentStatus || ! in_array($nextStatus, $currentStatus->allowedNextStatuses(), true)) {
                throw ValidationException::withMessages([
                    'status' => 'Perubahan status pesanan tidak diizinkan.',
                ]);
            }

            if ($nextStatus === OrderStatus::Cancelled) {
                foreach ($lockedOrder->items as $item) {
                    if (! $this->productService->restoreStock($item->product_id, $item->quantity)) {
                        throw new RuntimeException('Stok produk pesanan tidak dapat dikembalikan.');
                    }
                }
            }

            $lockedOrder->update(['status' => $nextStatus->value]);

            OrderStatusHistory::create([
                'order_id' => $lockedOrder->id,
                'status' => $nextStatus->value,
                'notes' => $notes,
                'changed_by' => $changedBy,
            ]);

            return [$lockedOrder->load('statusHistories'), $currentStatus->value];
        });

        event(new OrderStatusChanged($updatedOrder, $oldStatus, $nextStatus->value));

        return $updatedOrder;
    }
}
