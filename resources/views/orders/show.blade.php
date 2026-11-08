<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <a href="{{ route('orders.index') }}" class="text-sm font-medium text-indigo-700 hover:text-indigo-900">&larr; Kembali ke pesanan</a>
                <h2 class="mt-2 text-xl font-semibold leading-tight text-gray-800">Detail pesanan</h2>
            </div>
            @php($orderStatus = \Modules\Orders\Enums\OrderStatus::tryFrom($order->status))
            <span class="inline-flex w-fit rounded-full px-3 py-1.5 text-sm font-semibold {{ $orderStatus?->badgeClasses() ?? 'bg-gray-100 text-gray-700' }}">
                {{ $orderStatus?->label() ?? ucfirst($order->status) }}
            </span>
        </div>
    </x-slot>

    <div class="bg-gray-50 py-8 sm:py-12">
        <div class="mx-auto max-w-6xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <section class="grid gap-4 lg:grid-cols-[1.4fr_1fr]">
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                    <p class="text-sm text-gray-500">Nomor pesanan</p>
                    <h3 class="mt-1 break-all text-xl font-bold text-gray-900">{{ $order->order_number }}</h3>
                    <div class="mt-5 grid gap-4 sm:grid-cols-2">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Tanggal pesanan</p>
                            <p class="mt-1 text-sm font-medium text-gray-800">{{ $order->ordered_at->format('d M Y, H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Pelanggan</p>
                            <p class="mt-1 text-sm font-medium text-gray-800">{{ $order->user->name }}</p>
                            <p class="text-sm text-gray-500">{{ $order->user->email }}</p>
                        </div>
                    </div>
                </div>
                <div class="rounded-xl border border-indigo-100 bg-indigo-50 p-5 shadow-sm sm:p-6">
                    <p class="text-sm font-medium text-indigo-800">Total pesanan</p>
                    <p class="mt-2 text-3xl font-bold text-indigo-950">Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}</p>
                    <p class="mt-2 text-sm text-indigo-700">{{ $order->items->sum('quantity') }} barang dalam {{ $order->items->count() }} jenis produk</p>
                </div>
            </section>

            <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-5 py-4">
                    <h3 class="font-semibold text-gray-900">Rincian produk</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Produk</th>
                                <th scope="col" class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Harga</th>
                                <th scope="col" class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500">Jumlah</th>
                                <th scope="col" class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($order->items as $item)
                                <tr>
                                    <td class="px-5 py-4 text-sm font-medium text-gray-900">{{ $item->product_name }}</td>
                                    <td class="whitespace-nowrap px-5 py-4 text-right text-sm text-gray-600">Rp {{ number_format((float) $item->price_at_purchase, 0, ',', '.') }}</td>
                                    <td class="px-5 py-4 text-center text-sm text-gray-600">{{ $item->quantity }}</td>
                                    <td class="whitespace-nowrap px-5 py-4 text-right text-sm font-semibold text-gray-900">Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-gray-200 bg-gray-50 px-5 py-4">
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Alamat pengiriman</p>
                    <p class="mt-1 whitespace-pre-line text-sm text-gray-800">{{ $order->shipping_address ?: 'Tidak ada alamat pengiriman.' }}</p>
                </div>
            </section>

            <section class="grid gap-6 lg:grid-cols-[1fr_320px]">
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                    <h3 class="font-semibold text-gray-900">Riwayat status</h3>
                    @if ($order->statusHistories->isNotEmpty())
                        <ol class="mt-5 space-y-5">
                            @foreach ($order->statusHistories as $history)
                                @php($historyStatus = \Modules\Orders\Enums\OrderStatus::tryFrom($history->status))
                                <li class="relative flex gap-4">
                                    <span class="mt-1.5 h-2.5 w-2.5 shrink-0 rounded-full bg-indigo-500"></span>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                            <p class="text-sm font-semibold text-gray-900">{{ $historyStatus?->label() ?? ucfirst($history->status) }}</p>
                                            <time class="text-xs text-gray-500">{{ $history->created_at->format('d M Y, H:i') }}</time>
                                        </div>
                                        @if ($history->notes)
                                            <p class="mt-1 whitespace-pre-line text-sm text-gray-600">{{ $history->notes }}</p>
                                        @endif
                                        <p class="mt-1 text-xs text-gray-500">Oleh {{ $history->changedBy?->name ?? 'Sistem' }}</p>
                                    </div>
                                </li>
                            @endforeach
                        </ol>
                    @else
                        <p class="mt-4 text-sm text-gray-500">Belum ada riwayat status.</p>
                    @endif
                </div>

                @if ($isAdmin && count($availableStatuses) > 0)
                    <form action="{{ route('orders.status.update', $order) }}" method="POST" class="h-fit rounded-xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                        @csrf
                        @method('PATCH')
                        <h3 class="font-semibold text-gray-900">Perbarui status</h3>
                        <p class="mt-1 text-sm text-gray-500">Perubahan status dicatat dalam riwayat pesanan.</p>
                        <label for="status" class="mt-4 block text-sm font-medium text-gray-700">Status berikutnya</label>
                        <select name="status" id="status" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach ($availableStatuses as $status)
                                <option value="{{ $status->value }}">{{ $status->label() }}</option>
                            @endforeach
                        </select>
                        <label for="notes" class="mt-4 block text-sm font-medium text-gray-700">Catatan (opsional)</label>
                        <textarea name="notes" id="notes" rows="3" maxlength="1000" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                        <button type="submit" class="mt-4 w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">Simpan status</button>
                        @if (in_array(\Modules\Orders\Enums\OrderStatus::Cancelled, $availableStatuses, true))
                            <p class="mt-3 text-xs text-amber-700">Membatalkan pesanan akan mengembalikan stok produk.</p>
                        @endif
                    </form>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
