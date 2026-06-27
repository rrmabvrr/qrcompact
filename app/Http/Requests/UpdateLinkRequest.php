<?php
 
namespace App\Http\Requests;
 
use App\Rules\SafeUrl;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
 
class UpdateLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
 
    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:120'],
            'url' => ['required', 'string', 'url', 'regex:/^https?:\/\//i', new SafeUrl],
            'slug' => [
                'nullable',
                'string',
                'alpha_num',
                'min:3',
                'max:10',
                Rule::unique('links', 'slug')->ignore($this->route('slug'), 'slug'),
                'not_in:api,pix,whatsapp,up,login,cadastro,esqueci-senha,redefinir-senha,perfil,logout',
            ],
        ];
    }
 
    public function messages(): array
    {
        return [
            'name.string' => 'Informe um nome valido para o link.',
            'name.max' => 'O nome do link deve ter no maximo 120 caracteres.',
            'url.required' => 'Informe uma URL valida.',
            'url.url' => 'Informe uma URL valida.',
            'url.regex' => 'A URL deve comecar com http:// ou https://.',
            'slug.alpha_num' => 'O slug personalizado deve conter apenas letras e numeros.',
            'slug.min' => 'O slug personalizado deve ter pelo menos 3 caracteres.',
            'slug.max' => 'O slug personalizado deve ter no maximo 10 caracteres.',
            'slug.unique' => 'Este slug personalizado ja esta em uso.',
            'slug.not_in' => 'Este slug personalizado e reservado e nao pode ser usado.',
        ];
    }
}
