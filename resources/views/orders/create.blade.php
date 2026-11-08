<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium text-indigo-600">Manajemen transaksi</p>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Buat pesanan</h2>
            </div>
            <a href="{{ route('orders.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">&larr; Kembali ke pesanan</a>
        </div>
    </x-slot>

    <div class="bg-gray-50 py-8 sm:py-12">
        <div class="mx-auto max-w-5xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
                    <p class="font-semibold">Pesanan belum dapat dibuat.</p>
                    <ul class="mt-2 list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($products->isEmpty())
                <div class="rounded-xl border border-amber-200 bg-amber-50 p-5 text-sm text-amber-900">
                    Belum ada produk aktif yang memiliki stok. Pesanan belum dapat dibuat.
                </div>
            @else
                <form action="{{ route('orders.store') }}" method="POST"
                    x-data="{
                        items: {{ Illuminate\Support\Js::from(old('items', [['product_id' => '', 'quantity' => 1]])) }},
                        products: {{ Illuminate\Support\Js::from($products->map(fn ($product) => [
                            'id' => $product->id,
                            'name' => $product->name,
                            'price' => (float) $product->price,
                            'stock' => $product->stock,
                        ])->values()) }},
                        addItem() {
                            if (this.items.length < 20) this.items.push({ product_id: '', quantity: 1 });
                        },
                        removeItem(index) {
                            if (this.items.length > 1) this.items.splice(index, 1);
                        },
                        selectedProduct(item) {
                            return this.products.find((product) => Number(product.id) === Number(item.product_id));
                        },
                        subtotal(item) {
                            const product = this.selectedProduct(item);
                            return product ? product.price * Number(item.quantity || 0) : 0;
                        },
                        total() {
                            return this.items.reduce((sum, item) => sum + this.subtotal(item), 0);
                        },
                        formatCurrency(amount) {
                            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(amount);
                        }
                    }"
                    class="space-y-6">
                    @csrf

                    <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                        <div class="flex flex-col gap-3 border-b border-gray-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h3 class="font-semibold text-gray-900">Produk pesanan</h3>
                                <p class="mt-1 text-sm text-gray-500">Pilih produk dan jumlah yang ingin dipesan.</p>
                            </div>
                            <button type="button" @click="addItem()" :disabled="items.length >= 20" class="rounded-lg border border-indigo-200 px-3 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-50 disabled:cursor-not-allowed disabled:opacity-50">
                                + Tambah produk
                            </button>
                        </div>

                        <div class="space-y-4 p-5">
                            <template x-for="(item, index) in items" :key="index">
                                <div class="grid gap-4 rounded-lg border border-gray-200 p-4 sm:grid-cols-[minmax(0,1fr)_140px_auto] sm:items-end">
                                    <div>
                                        <label :for="'product-' + index" class="block text-sm font-medium text-gray-700">Produk</label>
                                        <select :id="'product-' + index" x-model="item.product_id" :name="'items[' + index + '][product_id]'" required
                                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="">Pilih produk</option>
                                            <template x-for="product in products" :key="product.id">
                                                <option :value="product.id" x-text="product.name + ' — ' + formatCurrency(product.price) + ' (stok ' + product.stock + ')'"></option>
                                            </template>
                                        </select>
                                        <p class="mt-1 text-xs text-gray-500" x-show="selectedProduct(item)" x-text="selectedProduct(item) ? 'Subtotal: ' + formatCurrency(subtotal(item)) : ''"></p>
                                    </div>
                                    <div>
                                        <label :for="'quantity-' + index" class="block text-sm font-medium text-gray-700">Jumlah</label>
                                        <input type="number" :id="'quantity-' + index" x-model.number="item.quantity" :name="'items[' + index + '][quantity]'"
                                            :max="selectedProduct(item)?.stock || 999" min="1" max="999" required
                                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                    <button type="button" @click="removeItem(index)" :disabled="items.length === 1"
                                        class="rounded-lg px-3 py-2 text-sm font-semibold text-red-600 hover:bg-red-50 disabled:cursor-not-allowed disabled:opacity-40">
                                        Hapus
                                    </button>
                                </div>
                            </template>

                            <div class="flex items-center justify-between rounded-lg bg-gray-50 px-4 py-3">
                                <span class="text-sm font-medium text-gray-600">Perkiraan total</span>
                                <span class="text-lg font-semibold text-gray-900" x-text="formatCurrency(total())"></span>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                        <label for="shipping_address" class="block text-sm font-semibold text-gray-900">Alamat pengiriman</label>
                        <p class="mt-1 text-sm text-gray-500">Pastikan alamat lengkap agar pesanan dapat dikirim dengan benar.</p>
                        <textarea name="shipping_address" id="shipping_address" rows="4" required maxlength="2000"
                            class="mt-3 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('shipping_address') }}</textarea>
                        @error('shipping_address')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </section>

                    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                        <a href="{{ route('orders.index') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-center text-sm font-semibold text-gray-700 hover:bg-gray-50">Batal</a>
                        <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">Buat pesanan</button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>
