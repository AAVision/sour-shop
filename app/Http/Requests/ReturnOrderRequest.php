<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReturnOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'order_id' => 'required|exists:orders,id',
            'reason' => 'required|string',
            'product_ids' => 'required|array',
            'product_ids.*' => 'required|exists:products,id',
            'bank_name' => 'nullable|string|max:200',
            'bank_account_number' => 'required|string',
            'return_address' => 'required|string',
        ];
    }
}
