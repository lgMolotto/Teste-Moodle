<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AtividadeRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Permite que a requisição seja processada
    }

    public function rules()
    {
        $uuid_disciplina = $this->route('uuid');

        $rules =  [
            'titulo'           => ['required', 'max:255'],
            'uuid_disciplina'  => ['required'],
            'descricao'        => ['nullable'],
            'pontuacao_maxima' => ['required', 'regex:/^\d{1,3}(\.\d{3})*.\d{2}$/'],
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'titulo.required'           => 'O titulo é obrigatório.',
            'titulo.max'                => 'O titulo é muito grande.',
            'uuid_disciplina.required'  => 'A disciplina é obrigatório.',
            'pontuacao_maxima.required' => 'A pontuação máxima é obrigatória.',
            'pontuacao_maxima.regex'    => 'A pontuação máxima é inválida.',
        ];
    }
}
