<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GenerateQrCodeRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $fields = ['mode', 'data', 'key_type', 'key', 'name', 'city', 'amount', 'txid'];
        $prepared = [];

        foreach ($fields as $field) {
            $value = $this->input($field);
            $prepared[$field] = is_string($value) ? trim($value) : $value;
        }

        $this->merge($prepared);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $pixMode = $this->input('mode') === 'pix';

        return [
            'mode' => ['nullable', Rule::in(['pix'])],
            'data' => [Rule::requiredIf(! $pixMode), 'nullable', 'string'],
            'key_type' => [Rule::requiredIf($pixMode), 'nullable', Rule::in(['phone', 'cpf', 'cnpj', 'email', 'random'])],
            'key' => [Rule::requiredIf($pixMode), 'nullable', 'string'],
            'name' => [Rule::requiredIf($pixMode), 'nullable', 'string'],
            'city' => [Rule::requiredIf($pixMode), 'nullable', 'string'],
            'amount' => ['nullable', 'string'],
            'txid' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'data.required' => 'Informe os dados para gerar o QR Code.',
            'key_type.required' => 'Selecione o tipo de chave Pix.',
            'key_type.in' => 'Tipo de chave Pix invalido.',
            'key.required' => 'Informe a chave Pix.',
            'name.required' => 'Informe o nome do beneficiario.',
            'city.required' => 'Informe a cidade do beneficiario.',
            'mode.in' => 'Modo de geracao invalido.',
        ];
    }

    public function isPixMode(): bool
    {
        return $this->validated('mode') === 'pix';
    }

    public function pixData(): array
    {
        return [
            'key_type' => $this->validated('key_type'),
            'key' => $this->validated('key'),
            'name' => $this->validated('name'),
            'city' => $this->validated('city'),
            'amount' => $this->validated('amount'),
            'txid' => $this->validated('txid'),
        ];
    }
}
