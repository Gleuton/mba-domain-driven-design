<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|min:3',
            'description' => 'string|max:1000|min:10',
            'date' => 'required|date|after:now',
            'partner_id' => 'required|uuid|exists:partners,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do evento é obrigatório.',
            'name.string' => 'O nome do evento deve ser uma sequência de caracteres.',
            'name.max' => 'O nome do evento não pode ter mais que 255 caracteres.',
            'name.min' => 'O nome do evento deve ter pelo menos 3 caracteres.',

            'description.string' => 'A descrição do evento deve ser uma sequência de caracteres.',
            'description.max' => 'A descrição do evento não pode ter mais que 1000 caracteres.',
            'description.min' => 'A descrição do evento deve ter pelo menos 10 caracteres.',

            'date.required' => 'A data do evento é obrigatória.',
            'date.date' => 'A data do evento deve ser uma data válida.',
            'date.after' => 'A data do evento deve ser uma data futura.',

            'partner_id.required' => 'O ID do parceiro é obrigatório.',
            'partner_id.uuid' => 'O ID do parceiro deve ser um UUID válido.',
            'partner_id.exists' => 'O parceiro selecionado não existe.',
        ];
    }
}
