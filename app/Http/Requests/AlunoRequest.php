<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AlunoRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Permite que a requisição seja processada
    }

    public function rules()
    {
        $uuid_aluno = $this->route('uuid');

        $rules =  [
            'nome'               => ['required', 'string', 'max:255'],
            'email'              => ['required', 'email', 'max:255', 'unique:aluno,email,' . $uuid_aluno . ',uuid_aluno'],
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'nome.required'  => 'O nome é obrigatório.',
            'nome.string'    => 'Insira um nome válido.',
            'nome.max'       => 'O nome é muito grande.',
            'email.required' => 'O email é obrigatório.',
            'email.email'    => 'insira um email válido.',
            'email.max'      => 'O email é muito grande.',
            'email.unique'   => 'E-mail já cadastrado para outro aluno.',
        ];
    }
}
