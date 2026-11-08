<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium text-indigo-600">Manajemen katalog</p>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Edit Produk
                </h2>
            </div>
            <a href="{{ route('products.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                &larr; Kembali ke daftar produk
            </a>
        </div>
    </x-slot>

    <div class="bg-gray-50 py-8 sm:py-12">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <p class="text-sm text-gray-500">Perbarui informasi produk dan simpan perubahan katalog.</p>
            </div>

            @if ($errors->any())
                <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
                    <p class="font-semibold">Periksa kembali data yang diisi.</p>
                    <ul class="mt-2 list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('products.update', $product) }}" method="POST" class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                @csrf
                @method('PUT')

                <div class="border-b border-gray-200 px-6 py-5 sm:px-8">
                    <h3 class="text-base font-semibold text-gray-900">Informasi produk</h3>
                    <p class="mt-1 text-sm text-gray-500">Nama dan slug digunakan untuk mengenali produk di katalog.</p>
                </div>

                <div class="grid gap-6 px-6 py-6 sm:grid-cols-2 sm:px-8">
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700">Nama produk</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" required maxlength="255"
                            class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="slug" class="block text-sm font-medium text-gray-700">Slug</label>
                        <input type="text" name="slug" id="slug" value="{{ old('slug', $product->slug) }}" required maxlength="255"
                            class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <p class="mt-1 text-xs text-gray-500">Slug harus unik dan digunakan pada alamat produk.</p>
                        @error('slug')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700">Harga (Rp)</label>
                        <input type="number" name="price" id="price" value="{{ old('price', $product->price) }}" required min="0" step="0.01"
                            class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="stock" class="block text-sm font-medium text-gray-700">Stok</label>
                        <input type="number" name="stock" id="stock" value="{{ old('stock', $product->stock) }}" required min="0" step="1"
                            class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('stock')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="low_stock_threshold" class="block text-sm font-medium text-gray-700">Batas stok rendah</label>
                        <input type="number" name="low_stock_threshold" id="low_stock_threshold" value="{{ old('low_stock_threshold', $product->low_stock_threshold) }}" required min="0" step="1"
                            class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('low_stock_threshold')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center gap-3 self-end rounded-lg border border-gray-200 p-4">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" id="is_active" value="1" @checked((bool) old('is_active', $product->is_active))
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <label for="is_active" class="text-sm font-medium text-gray-700">Produk aktif dan tersedia</label>
                        @error('is_active')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                        <textarea name="description" id="description" rows="5" maxlength="5000"
                            class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $product->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="border-t border-gray-200 px-6 py-6 sm:px-8">
                    <input type="hidden" name="sync_categories" value="1">
                    <h3 class="text-base font-semibold text-gray-900">Kategori</h3>
                    <p class="mt-1 text-sm text-gray-500">Pilih semua kategori yang sesuai. Hapus semua pilihan untuk melepas kategori.</p>

                    @if ($categories->isNotEmpty())
                        <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($categories as $category)
                                <label for="category-{{ $category->id }}" class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-200 p-3 hover:border-indigo-300 hover:bg-indigo-50/50">
                                    <input type="checkbox" name="category_ids[]" id="category-{{ $category->id }}" value="{{ $category->id }}"
                                        @checked(in_array($category->id, (array) old('category_ids', $product->categories->modelKeys())))
                                        class="mt-0.5 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <span class="text-sm font-medium text-gray-700">{{ $category->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <p class="mt-4 rounded-lg bg-gray-50 p-4 text-sm text-gray-600">Belum ada kategori. Produk tetap dapat disimpan tanpa kategori.</p>
                    @endif
                    @error('category_ids')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('category_ids.*')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-gray-200 bg-gray-50 px-6 py-5 sm:flex-row sm:justify-end sm:px-8">
                    <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                        Batal
                    </a>
                    <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Simpan perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
