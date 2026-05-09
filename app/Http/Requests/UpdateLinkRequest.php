<?php

namespace App\Http\Requests;

use App\Rules\SafeUrl;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'url' => ['required', 'string', 'url', 'regex:/^https?:\/\//i', new SafeUrl],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Informe um nome para o link.',
            'name.string' => 'Informe um nome valido para o link.',
            'name.max' => 'O nome do link deve ter no maximo 120 caracteres.',
            'url.required' => 'Informe uma URL valida.',
            'url.url' => 'Informe uma URL valida.',
            'url.regex' => 'A URL deve comecar com http:// ou https://.',
        ];
    }
}
