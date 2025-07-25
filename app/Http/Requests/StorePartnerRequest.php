<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePartnerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|min:3',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do parceiro é obrigatório.',
            'name.min' => 'O nome do parceiro deve ter pelo menos 3 caracteres.',
            'name.max' => 'O nome do parceiro não pode ter mais de 255 caracteres',
        ];
    }
}
