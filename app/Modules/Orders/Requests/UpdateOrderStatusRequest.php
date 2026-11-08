<?php

namespace Modules\Orders\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Orders\Enums\OrderStatus;

class UpdateOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->roles()->where('slug', 'admin')->exists() ?? false;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(OrderStatus::values())],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Pilih status pesanan.',
            'status.in' => 'Status pesanan tidak valid.',
            'notes.max' => 'Catatan maksimal 1000 karakter.',
        ];
    }
}
