<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'url' => ['required', 'string', 'url', 'regex:/^https?:\/\//i'],
        ];
    }

    public function messages(): array
    {
        return [
            'url.required' => 'Informe uma URL valida.',
            'url.url' => 'Informe uma URL valida.',
            'url.regex' => 'A URL deve comecar com http:// ou https://.',
        ];
    }
}
