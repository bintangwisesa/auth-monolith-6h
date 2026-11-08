<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use Modules\Products\Models\Category;
use Modules\Products\Models\Product;

class LandingPageController extends Controller
{
    public function __invoke(): View
    {
        $products = Product::available()
            ->with(['categories:id,name'])
            ->orderByDesc('created_at')
            ->limit(4)
            ->get();

        $categories = Category::query()
            ->whereHas('products', fn (Builder $query): Builder => $query->available())
            ->withCount([
                'products as available_products_count' => fn (Builder $query): Builder => $query->available(),
            ])
            ->orderBy('name')
            ->limit(4)
            ->get();

        return view('landing.index', [
            'products' => $products,
            'categories' => $categories,
            'productCount' => Product::available()->count(),
            'categoryCount' => Category::query()
                ->whereHas('products', fn (Builder $query): Builder => $query->available())
                ->count(),
        ]);
    }
}
