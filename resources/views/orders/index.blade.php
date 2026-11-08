<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium text-indigo-600">Manajemen transaksi</p>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Pesanan</h2>
            </div>
            <a href="{{ route('orders.create') }}" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
                + Buat pesanan
            </a>
        </div>
    </x-slot>

    <div class="bg-gray-50 py-8 sm:py-12">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid gap-4 sm:grid-cols-3">
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Total pesanan</p>
                    <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $summary['total'] }}</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Menunggu diproses</p>
                    <p class="mt-2 text-3xl font-semibold text-amber-600">{{ $summary['pending'] }}</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Dalam proses / pengiriman</p>
                    <p class="mt-2 text-3xl font-semibold text-indigo-600">{{ $summary['processing'] }}</p>
                </div>
            </div>

            <form action="{{ route('orders.index') }}" method="GET" class="grid gap-3 rounded-xl border border-gray-200 bg-white p-4 shadow-sm sm:grid-cols-[1fr_220px_auto_auto] sm:items-end">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700">Nomor pesanan</label>
                    <input type="search" name="search" id="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari nomor pesanan"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Semua status</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" @selected(($filters['status'] ?? '') === $status->value)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-gray-700">Terapkan</button>
                <a href="{{ route('orders.index') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-center text-sm font-semibold text-gray-700 hover:bg-gray-50">Reset</a>
            </form>

            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="flex flex-col gap-1 border-b border-gray-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <h3 class="font-semibold text-gray-900">Daftar pesanan</h3>
                    @if ($isAdmin)
                        <p class="text-sm text-gray-500">Menampilkan pesanan dari semua pelanggan.</p>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Pesanan</th>
                                @if ($isAdmin)
                                    <th scope="col" class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Pelanggan</th>
                                @endif
                                <th scope="col" class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Tanggal</th>
                                <th scope="col" class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500">Item</th>
                                <th scope="col" class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Total</th>
                                <th scope="col" class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500">Status</th>
                                <th scope="col" class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500"><span class="sr-only">Aksi</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($orders as $order)
                                @php($orderStatus = \Modules\Orders\Enums\OrderStatus::tryFrom($order->status))
                                <tr class="hover:bg-gray-50">
                                    <td class="whitespace-nowrap px-5 py-4">
                                        <a href="{{ route('orders.show', $order) }}" class="font-semibold text-indigo-700 hover:text-indigo-900">{{ $order->order_number }}</a>
                                        <p class="mt-1 text-xs text-gray-500">#{{ $order->id }}</p>
                                    </td>
                                    @if ($isAdmin)
                                        <td class="whitespace-nowrap px-5 py-4">
                                            <p class="text-sm font-medium text-gray-800">{{ $order->user->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $order->user->email }}</p>
                                        </td>
                                    @endif
                                    <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-600">{{ $order->ordered_at->format('d M Y, H:i') }}</td>
                                    <td class="whitespace-nowrap px-5 py-4 text-center text-sm text-gray-600">{{ $order->items_count }}</td>
                                    <td class="whitespace-nowrap px-5 py-4 text-right text-sm font-semibold text-gray-900">Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}</td>
                                    <td class="whitespace-nowrap px-5 py-4 text-center">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $orderStatus?->badgeClasses() ?? 'bg-gray-100 text-gray-700' }}">
                                            {{ $orderStatus?->label() ?? ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4 text-right">
                                        <a href="{{ route('orders.show', $order) }}" class="text-sm font-semibold text-indigo-700 hover:text-indigo-900">Detail</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $isAdmin ? 7 : 6 }}" class="px-5 py-14 text-center">
                                        <p class="font-medium text-gray-900">Belum ada pesanan</p>
                                        <p class="mt-1 text-sm text-gray-500">Buat pesanan baru untuk mulai bertransaksi.</p>
                                        <a href="{{ route('orders.create') }}" class="mt-4 inline-flex rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Buat pesanan</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($orders->hasPages())
                    <div class="border-t border-gray-200 px-5 py-4">{{ $orders->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
