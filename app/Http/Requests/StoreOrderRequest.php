<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'event_id' => 'required|string|max:255|min:3',
            'customer_id' => 'required|string|max:255|min:3',
            'event_section_id' => 'required|string|max:255|min:3',
            'event_spot_id' => 'required|string|max:255|min:3',
        ];
    }

    public function messages(): array
    {
        return [
            'event_id.required' => 'o campo event_id é obrigatório.',
            'event_id.string' => 'o campo event_id deve ser uma string.',
            'event_id.max' => 'o campo event_id deve ter no máximo 255 caracteres.',
            'event_id.min' => 'o campo event_id deve ter no mínimo 3 caracteres',

            'customer_id.required' => 'o campo customer_id é obrigatório.',
            'customer_id.string' => 'o campo customer_id deve ser uma string.',
            'customer_id.max' => 'o campo customer_id deve ter no máximo 255 caracteres',
            'customer_id.min' => 'o campo customer_id deve ter no mínimo 3 caracteres',

            'event_section_id.required' => 'o campo event_section_id é obrigatório.',
            'event_section_id.string' => 'o campo event_section_id deve ser uma string.',
            'event_section_id.max' => 'o campo event_section_id deve ter no máximo 255 caracteres',
            'event_section_id.min' => 'o campo event_section_id deve ter no mínimo 3 caracteres',

            'event_spot_id.required' => 'o campo event_spot_id é obrigatório.',
            'event_spot_id.string' => 'o campo event_spot_id deve ser uma string.',
            'event_spot_id.max' => 'o campo event_spot_id deve ter no máximo 255 caracteres',
            'event_spot_id.min' => 'o campo event_spot_id deve ter no mínimo 3 caracteres',
        ];
    }
}
