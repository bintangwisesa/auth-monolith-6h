<?php

namespace Modules\Orders\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Modules\Orders\Enums\OrderStatus;
use Modules\Orders\Models\Order;
use Modules\Orders\Requests\StoreOrderRequest;
use Modules\Orders\Requests\UpdateOrderStatusRequest;
use Modules\Orders\Services\OrderService;
use Modules\Products\Models\Product;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'status' => ['nullable', Rule::in(OrderStatus::values())],
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $orders = $this->visibleOrdersQuery($request->user())
            ->with('user:id,name,email')
            ->withCount('items')
            ->when(
                $filters['status'] ?? null,
                fn (Builder $query, string $status): Builder => $query->where('status', $status),
            )
            ->when(
                $filters['search'] ?? null,
                fn (Builder $query, string $search): Builder => $query->where('order_number', 'like', "%{$search}%"),
            )
            ->orderByDesc('ordered_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $summary = [
            'total' => $this->visibleOrdersQuery($request->user())->count(),
            'pending' => $this->visibleOrdersQuery($request->user())->where('status', OrderStatus::Pending->value)->count(),
            'processing' => $this->visibleOrdersQuery($request->user())->whereIn('status', [
                OrderStatus::Processing->value,
                OrderStatus::Shipped->value,
            ])->count(),
        ];

        return view('orders.index', [
            'orders' => $orders,
            'statuses' => OrderStatus::cases(),
            'filters' => $filters,
            'summary' => $summary,
            'isAdmin' => $this->isAdmin($request->user()),
        ]);
    }

    public function create(): View
    {
        $products = Product::available()
            ->orderBy('name')
            ->get(['id', 'name', 'price', 'stock']);

        return view('orders.create', compact('products'));
    }

    public function store(StoreOrderRequest $request, OrderService $orderService): RedirectResponse
    {
        $order = $orderService->createOrder(
            $request->user()->id,
            $request->validated('items'),
            $request->validated('shipping_address'),
        );

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Pesanan berhasil dibuat.');
    }

    public function show(Request $request, Order $order): View
    {
        $order = $this->visibleOrdersQuery($request->user())
            ->with([
                'user:id,name,email',
                'items',
                'statusHistories.changedBy:id,name',
            ])
            ->findOrFail($order->id);

        $currentStatus = OrderStatus::tryFrom($order->status);

        return view('orders.show', [
            'order' => $order,
            'availableStatuses' => $currentStatus?->allowedNextStatuses() ?? [],
            'isAdmin' => $this->isAdmin($request->user()),
        ]);
    }

    public function updateStatus(
        UpdateOrderStatusRequest $request,
        Order $order,
        OrderService $orderService,
    ): RedirectResponse {
        $orderService->updateStatus(
            $order,
            $request->validated('status'),
            $request->user()->id,
            $request->validated('notes'),
        );

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Status pesanan berhasil diperbarui.');
    }

    private function visibleOrdersQuery(User $user): Builder
    {
        $query = Order::query();

        if (! $this->isAdmin($user)) {
            $query->where('user_id', $user->id);
        }

        return $query;
    }

    private function isAdmin(User $user): bool
    {
        return $user->roles()->where('slug', 'admin')->exists();
    }
}
