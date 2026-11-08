<?php

namespace Modules\Products\Controllers;

use App\Http\Controllers\Controller;
use Modules\Products\Exceptions\InsufficientStockException;
use Modules\Products\Models\Category;
use Modules\Products\Models\Product;
use Modules\Products\Requests\StoreProductRequest;
use Modules\Products\Requests\UpdateProductRequest;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::paginate(10);

        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(StoreProductRequest $request)
    {
        $product = Product::create($request->validated());

        return redirect()
            ->route('products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $product->load('categories');
        $categories = Category::query()->orderBy('name')->get();

        return view('products.edit', compact('product', 'categories'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $validated = $request->validated();
        $product->update($request->safe()->only([
            'name',
            'slug',
            'description',
            'price',
            'stock',
            'low_stock_threshold',
            'is_active',
        ]));

        if ($validated['sync_categories'] ?? false) {
            $product->categories()->sync($validated['category_ids'] ?? []);
        }

        return redirect()
            ->route('products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    public function reduceStock(Product $product, int $quantity)
    {
        if ($product->stock < $quantity) {
            throw new InsufficientStockException(
                $product->name,
                $product->stock,
                $quantity
            );
        }

        $product->decrement('stock', $quantity);

        return redirect()
            ->route('products.index')
            ->with('success', 'Stok berhasil dikurangi.');
    }
}
