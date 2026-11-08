<?php

namespace Modules\Orders\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1', 'max:20'],
            'items.*.product_id' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('products', 'id')->where('is_active', true),
            ],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:999'],
            'shipping_address' => ['required', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Tambahkan minimal satu produk ke pesanan.',
            'items.min' => 'Tambahkan minimal satu produk ke pesanan.',
            'items.max' => 'Pesanan maksimal berisi 20 jenis produk.',
            'items.*.product_id.required' => 'Pilih produk untuk setiap baris pesanan.',
            'items.*.product_id.distinct' => 'Produk yang sama tidak boleh ditambahkan lebih dari sekali.',
            'items.*.product_id.exists' => 'Produk yang dipilih tidak tersedia.',
            'items.*.quantity.required' => 'Jumlah produk wajib diisi.',
            'items.*.quantity.min' => 'Jumlah produk minimal satu.',
            'shipping_address.required' => 'Alamat pengiriman wajib diisi.',
        ];
    }
}
