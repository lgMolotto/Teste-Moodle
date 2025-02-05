<?php

namespace App\Http\Controllers;

use App\Models\AlunoModel;
use App\Models\DisciplinaModel;
use App\Models\MatriculaModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;

class MatriculaController extends BaseController
{
    // Matricula um aluno na disciplina
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $uuid_aluno = $request->get('uuid_aluno');
            $uuid_disciplina = $request->get('uuid_disciplina');

            if (!$uuid_aluno || !$uuid_disciplina) {
                return response()->json([
                    'success' => false,
                    'message' => 'Informe o aluno e a disciplina.',
                    'errors'  => 'Aluno ou disciplina não informados'
                ], 422);
            }

            // Busca o aluno que vai ser matriculado
            $aluno = AlunoModel::where('uuid_aluno', $uuid_aluno)
                ->first();

            if (!$aluno) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aluno não encontrado.',
                    'errors'  => ''
                ], 404);
            }

            // Busca a disciplina que o aluno vai ser matriculado
            $disciplina = DisciplinaModel::where('uuid_disciplina', $uuid_disciplina)
                ->first();

            if (!$disciplina) {
                return response()->json([
                    'success' => false,
                    'message' => 'Disciplina não encontrado.',
                    'errors'  => ''
                ], 404);
            }

            // Verifica se ele já não tem uma matricula ativa nessa disciplina
            $matricula_ativa = MatriculaModel::where('codigo_disciplina', $disciplina->codigo_disciplina)
                ->where('codigo_aluno', $aluno->codigo_aluno)
                ->first();

            if ($matricula_ativa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aluno já está matriculado nessa disciplina.',
                    'errors'  => ''
                ], 409);
            }

            // Cria a relação entre aluno e disciplina
            $matricula = MatriculaModel::create(
                [
                    'codigo_aluno'      => $aluno->codigo_aluno,
                    'codigo_disciplina' => $disciplina->codigo_disciplina,
                ]
            );

            if (!$matricula) {
                throw new Exception();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Aluno matriculado com sucesso!',
                'data'    => $aluno
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao matricular o aluno na disciplina.',
                'errors'  => $th->getMessage()
            ], 400);
        }
    }

    // Desmatricula um aluno na disciplina (inativa a matricula)
    public function delete($uuid_matricula)
    {
        try {
            DB::beginTransaction();
            // Busca a matricula que vai ser inativada
            $matricula = MatriculaModel::where('uuid_matricula', $uuid_matricula)
                ->first();

            if (!$matricula) {
                return response()->json([
                    'success' => false,
                    'message' => 'Matricula não encontrado.',
                    'errors'  => ''
                ], 404);
            }

            $matricula->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Matricula inativada com sucesso!',
                'data'    => ''
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao inativar a matricula.',
                'errors'  => $th->getMessage()
            ], 400);
        }
    }

    // Reativa a matricula do aluno
    public function restore($uuid_matricula)
    {
        try {
            DB::beginTransaction();
            // Busca a matricula que vai ser inativada
            $matricula = MatriculaModel::withTrashed()
                ->where('uuid_matricula', $uuid_matricula)
                ->first();

            if (!$matricula) {
                return response()->json([
                    'success' => false,
                    'message' => 'Matricula não encontrado.',
                    'errors'  => ''
                ], 404);
            }

            $matricula->restore();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Matricula reativada com sucesso!',
                'data'    => ''
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao reativar a matricula.',
                'errors'  => $th->getMessage()
            ], 400);
        }
    }
}
