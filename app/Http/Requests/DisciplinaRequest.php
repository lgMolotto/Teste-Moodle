<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DisciplinaRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Permite que a requisição seja processada
    }

    public function rules()
    {
        $uuid_professor = $this->route('uuid');

        $rules =  [
            'nome'           => ['required', 'max:255'],
            'uuid_professor' => ['required'],
            'descricao'      => ['required'],
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'nome.required'            => 'O nome é obrigatório.',
            'nome.max'                 => 'O nome é muito grande.',
            'uuid_professor.required'  => 'O professor é obrigatório.',
            'descricao.required'       => 'A descrição é obrigatória.',
        ];
    }
}
