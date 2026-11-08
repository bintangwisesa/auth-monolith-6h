<?php

namespace Modules\Products\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product')->id;

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('products', 'slug')->ignore($productId)],
            'price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'stock' => ['sometimes', 'required', 'integer', 'min:0'],
            'low_stock_threshold' => ['sometimes', 'required', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'required', 'boolean'],
            'description' => ['nullable', 'string', 'max:5000'],
            'sync_categories' => ['sometimes', 'boolean'],
            'category_ids' => ['sometimes', 'array'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.max' => 'Nama produk maksimal 255 karakter.',
            'slug.unique' => 'Slug produk sudah digunakan.',
            'price.numeric' => 'Harga harus berupa angka.',
            'price.min' => 'Harga tidak boleh negatif.',
            'stock.integer' => 'Stok harus berupa bilangan bulat.',
            'stock.min' => 'Stok tidak boleh negatif.',
            'low_stock_threshold.required' => 'Batas stok rendah wajib diisi.',
            'low_stock_threshold.integer' => 'Batas stok rendah harus berupa bilangan bulat.',
            'low_stock_threshold.min' => 'Batas stok rendah tidak boleh negatif.',
            'category_ids.*.exists' => 'Kategori yang dipilih tidak valid.',
        ];
    }
}
