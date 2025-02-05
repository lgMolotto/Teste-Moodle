<?php

namespace App\Http\Controllers;

use App\Http\Requests\AtividadeRequest;
use App\Models\AlunoModel;
use App\Models\AtividadeModel;
use App\Models\DisciplinaModel;
use App\Models\MatriculaModel;
use App\Models\PontuacaoModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;

class AtividadeController extends BaseController
{
    // Busca todas as atividades
    public function index()
    {
        try {
            $atividades = AtividadeModel::get();

            return response()->json([
                'success' => true,
                'message' => '',
                'data' => $atividades
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao buscar as atividades.',
                'errors' => $th->getMessage()
            ], 400);
        }
    }

    // Busca uma atividade específica pelo uuid dela
    public function show($uuid_disciplina)
    {
        try {
            $atividade = AtividadeModel::where('uuid_disciplina', $uuid_disciplina)
                ->first();

            // Verifica se encontrou a atividade
            if (!$atividade) {
                return response()->json([
                    'success' => true,
                    'message' => 'Atividade não encontrada',
                    'data'    => ''
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => '',
                'data'    => $atividade
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao buscar a atividade.',
                'errors' => $th->getMessage()
            ], 400);
        }
    }

    // Salva uma nova atividade
    public function store(AtividadeRequest $request)
    {
        try {
            DB::beginTransaction();

            $dados = $request->validated();

            // Busca a disciplina
            $disciplina = DisciplinaModel::where('uuid_disciplina', $dados['uuid_disciplina'])
                ->first();

            if (!$disciplina) {
                return response()->json([
                    'success' => true,
                    'message' => 'Disciplina não encontrada',
                    'data'    => ''
                ], 404);
            }

            // Seta o codigo da disciplina e remove o uuid do array
            $dados['codigo_disciplina'] = $disciplina->codigo_disciplina;
            unset($dados['uuid_disciplina']);

            // Seta o status da atividade (sempre cadastra uma como Pendente)
            $dados['status'] = "P";

            $atividade = AtividadeModel::create($dados);

            if (!$atividade) {
                throw new Exception();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Atividade cadastrada com sucesso!',
                'data' => $atividade
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao cadastrar a atividade.',
                'errors' => $th->getMessage()
            ], 400);
        }
    }

    // Atualiza uma atividade
    public function update(AtividadeRequest $request, $uuid_atividade)
    {
        try {
            DB::beginTransaction();

            $atividade = AtividadeModel::where('uuid_atividade', $uuid_atividade)->first();

            if (!$atividade) {
                return response()->json([
                    'success' => true,
                    'message' => 'Atividade não encontrada',
                    'data'    => ''
                ], 404);
            }
            $dados = $request->validated();

            // Busca a disciplina
            $disciplina = DisciplinaModel::where('uuid_disciplina', $dados['uuid_disciplina'])
                ->first();

            if (!$disciplina) {
                return response()->json([
                    'success' => true,
                    'message' => 'Disciplina não encontrada',
                    'data'    => ''
                ], 404);
            }

            // Seta o codigo da disciplina e remove o uuid do array
            $dados['codigo_disciplina'] = $disciplina['codigo_disciplina'];
            unset($dados['uuid_disciplina']);

            $atividade->update($dados);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Atividade editada com sucesso!',
                'data' => $atividade
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao editar a atividade.',
                'errors' => $th->getMessage()
            ], 400);
        }
    }

    // Inativa uma atividade
    public function delete($uuid_atividade)
    {
        try {
            DB::beginTransaction();

            $atividade = AtividadeModel::where('uuid_atividade', $uuid_atividade)->first();

            if (!$atividade) {
                return response()->json([
                    'success' => true,
                    'message' => 'Atividade não encontrada',
                    'data'    => ''
                ], 404);
            }

            $atividade->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Atividade inativada com sucesso!',
                'data' => $atividade
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao inativar a atividade.',
                'errors' => $th->getMessage()
            ], 400);
        }
    }

    // Ativa uma atividade
    public function restore($uuid_atividade)
    {
        try {
            DB::beginTransaction();

            $atividade = AtividadeModel::withTrashed()
                ->where('uuid_atividade', $uuid_atividade)->first();

            if (!$atividade) {
                return response()->json([
                    'success' => true,
                    'message' => 'Atividade não encontrada',
                    'data'    => ''
                ], 404);
            }

            $atividade->restore();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Atividade reativada com sucesso!',
                'data' => $atividade
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao reativar a atividade.',
                'errors' => $th->getMessage()
            ], 400);
        }
    }

    // Finaliza a atividade (troca o status para Avaliada)
    public function finalizar($uuid_atividade)
    {
        try {
            DB::beginTransaction();

            $atividade = AtividadeModel::where('uuid_atividade', $uuid_atividade)
                ->first();

            if (!$atividade) {
                return response()->json([
                    'success' => true,
                    'message' => 'Atividade não encontrada',
                    'data'    => ''
                ], 404);
            }

            $atividade->status = 'A';
            $atividade->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Atividade finalizada com sucesso!',
                'data' => $atividade
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao finalizar a atividade.',
                'errors' => $th->getMessage()
            ], 400);
        }
    }

    // Avalia a atividade do aluno
    public function avaliarAluno(Request $request)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'uuid_atividade' => 'required',
                'uuid_aluno'     => 'required',
                'nota'           => ['required', 'regex:/^\d+(\.\d{2})?$/', 'numeric', 'between:0,100.00'],
            ], [
                'uuid_atividade.required' => 'Favor informe a atividade.',
                'uuid_aluno.required'     => 'Favor informe o aluno.',
                'nota.required'           => 'Favor informe a nota.',
                'nota.regex'              => 'A nota deve estar no formato xx.xx.',
                'nota.numeric'            => 'A nota não deve conter outros caracteres além de números e ponto.',
                'nota.between'            => 'Informe uma nota entre 0.00 e 100.00.',
            ]);

            $uuid_atividade = $request->get('uuid_atividade');
            $uuid_aluno = $request->get('uuid_aluno');
            $nota = $request->get('nota');

            // Busca a atividade
            $atividade = AtividadeModel::where('uuid_atividade', $uuid_atividade)
                ->first();

            if (!$atividade) {
                return response()->json([
                    'success' => false,
                    'message' => 'Atividade não encontrada',
                    'data'    => ''
                ], 404);
            }

            // Busca o aluno
            $aluno = AlunoModel::where('uuid_aluno', $uuid_aluno)
                ->first();

            if (!$aluno) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aluno não encontrado',
                    'data'    => ''
                ], 404);
            }

            // Busca a matricula desse aluno na disciplina referente a atividade
            $matricula = MatriculaModel::where('codigo_aluno', $aluno->codigo_aluno)
                ->where('codigo_disciplina', $atividade->codigo_disciplina)
                ->first();

            // Verifica se a ativida pertence a uma disciplina que o aluno está matriculado
            if (!$matricula) {
                return response()->json([
                    'success' => false,
                    'message' => 'O aluno não está matriculado na disciplina dessa atividade',
                    'data'    => ''
                ], 409);
            }

            // Verifica se a nota não é maior que a nota máxima da disciplina
            if ($nota > $atividade->pontuacao_maxima) {
                return response()->json([
                    'success' => false,
                    'message' => 'A nota é maior que a pontuação máxima da atividade',
                    'data'    => ''
                ], 409);
            }

            // Cria ou atualiza a relação entre o aluno, atividade e atribui a nota dele
            PontuacaoModel::updateOrCreate(
                [
                    'codigo_aluno'     => $aluno->codigo_aluno,
                    'codigo_atividade' => $atividade->codigo_atividade,
                ],
                [
                    'nota' => $nota
                ]
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Nota atribuida com sucesso!',
                'data' => ''
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao atribuir a nota.',
                'errors' => $th->getMessage()
            ], 400);
        }
    }
}
