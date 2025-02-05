<?php

namespace App\Http\Controllers;

use App\Http\Requests\DisciplinaRequest;
use App\Models\AtividadeModel;
use App\Models\DisciplinaModel;
use App\Models\MatriculaModel;
use App\Models\ProfessorModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;

class DisciplinaController extends BaseController
{
    // Busca todas as disciplinas
    public function index()
    {
        try {
            $disciplinas = DisciplinaModel::get();

            return response()->json([
                'success' => true,
                'message' => '',
                'data' => $disciplinas
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao buscar as disciplinas.',
                'errors' => $th->getMessage()
            ], 400);
        }
    }

    // Busca uma disciplina específica pelo uuid dela
    public function show($uuid_disciplina)
    {
        try {
            $disciplina = DisciplinaModel::where('uuid_disciplina', $uuid_disciplina)
                ->first();

            // Verifica se encontrou a disciplina
            if (!$disciplina) {
                return response()->json([
                    'success' => true,
                    'message' => 'Disciplina não encontrada',
                    'data'    => ''
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => '',
                'data'    => $disciplina
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao buscar a disciplina.',
                'errors' => $th->getMessage()
            ], 400);
        }
    }

    // Salva uma nova disciplina
    public function store(DisciplinaRequest $request)
    {
        try {
            DB::beginTransaction();

            $dados = $request->validated();
            $codigo_professor = ProfessorModel::select('codigo_professor')
                ->where('uuid_professor', $dados['uuid_professor'])
                ->first()->codigo_professor;

            $dados['codigo_professor'] = $codigo_professor;
            unset($dados['uuid_professor']);

            $disciplina = DisciplinaModel::create($dados);

            if (!$disciplina) {
                throw new Exception();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Disciplina cadastrada com sucesso!',
                'data' => $disciplina
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao cadastrar a disciplina.',
                'errors' => $th->getMessage()
            ], 400);
        }
    }

    // Atualiza uma disciplina
    public function update(DisciplinaRequest $request, $uuid_disciplina)
    {
        try {
            DB::beginTransaction();

            $disciplina = DisciplinaModel::where('uuid_disciplina', $uuid_disciplina)->first();

            if (!$disciplina) {
                return response()->json([
                    'success' => true,
                    'message' => 'Disciplina não encontrada',
                    'data'    => ''
                ], 404);
            }

            $disciplina->update($request->validated());

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Disciplina editada com sucesso!',
                'data' => $disciplina
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao editar a disciplina.',
                'errors' => $th->getMessage()
            ], 400);
        }
    }

    // Inativa uma disciplina
    public function delete($uuid_disciplina)
    {
        try {
            DB::beginTransaction();

            $disciplina = DisciplinaModel::where('uuid_disciplina', $uuid_disciplina)->first();

            if (!$disciplina) {
                return response()->json([
                    'success' => true,
                    'message' => 'Disciplina não encontrada',
                    'data'    => ''
                ], 404);
            }

            $disciplina->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Disciplina inativada com sucesso!',
                'data' => $disciplina
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao inativar a disciplina.',
                'errors' => $th->getMessage()
            ], 400);
        }
    }

    // Ativa uma disciplina
    public function restore($uuid_disciplina)
    {
        try {
            DB::beginTransaction();

            $disciplina = DisciplinaModel::withTrashed()
                ->where('uuid_disciplina', $uuid_disciplina)->first();

            if (!$disciplina) {
                return response()->json([
                    'success' => true,
                    'message' => 'Disciplina não encontrada',
                    'data'    => ''
                ], 404);
            }

            $disciplina->restore();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Disciplina reativada com sucesso!',
                'data' => $disciplina
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao reativar a disciplina.',
                'errors' => $th->getMessage()
            ], 400);
        }
    }

    // Busca as atividades de uma disciplina
    public function atividades($uuid_disciplina)
    {
        try {
            $atividades = DisciplinaModel::select('atividade.*')
                ->where('uuid_disciplina', $uuid_disciplina)
                ->join('atividade', 'disciplina.codigo_disciplina', 'atividade.codigo_disciplina')
                ->get();

            // Verifica se encontrou o professor
            if (!$atividades) {
                return response()->json([
                    'success' => true,
                    'message' => 'Atividades da disciplina não encontradas',
                    'data'    => ''
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => '',
                'data'    => $atividades
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao buscar as atividades da disciplina.',
                'errors' => $th->getMessage()
            ], 400);
        }
    }

    // Busca as medias da disciplina
    public function medias($uuid_disciplina)
    {
        try {
            // Busca a disciplina pelo uuid
            $disciplina = DisciplinaModel::where('uuid_disciplina', $uuid_disciplina)
                ->first();

            if (!$disciplina) {
                return response()->json([
                    'success' => false,
                    'message' => 'Disciplina não encontrada',
                    'data'    => ''
                ], 404);
            }

            // Busca os alunos que estão matriculados nessa disciplina
            $alunos = MatriculaModel::select('aluno.nome', 'aluno.codigo_aluno')
                ->where('codigo_disciplina', $disciplina->codigo_disciplina)
                ->join('aluno', 'matricula.codigo_aluno', 'aluno.codigo_aluno')
                ->get();

            if (!$alunos) {
                return response()->json([
                    'success' => false,
                    'message' => 'Essa disciplina não tem nenhum aluno matriculado',
                    'data'    => ''
                ], 404);
            }

            foreach ($alunos as $key => $aluno) {
                $dados[$key]['nome'] = $aluno['nome'];

                // Busca a nota das atividades desse aluno
                $pontos = AtividadeModel::select('pontuacao.nota')
                    ->join('pontuacao', 'atividade.codigo_atividade', 'pontuacao.codigo_atividade')
                    ->where('atividade.codigo_disciplina', $disciplina['codigo_disciplina'])
                    ->where('pontuacao.codigo_aluno', $aluno['codigo_aluno'])
                    ->get()->toArray();

                if ($pontos) {
                    // Calcula as média dos alunos
                    $media = array_sum(array_column($pontos, 'nota')) / count($pontos);
                    $media = round($media, 2);
                    $dados[$key]['media'] = $media;
                } else {
                    $dados[$key]['media'] = 0.00;
                }
            }

            // Retorna a média da disciplina
            $dados['media_disciplina'] = round(array_sum(array_column($dados, 'media')) / count($alunos), 2);

            return response()->json([
                'success' => true,
                'message' => 'Médias da disciplina',
                'data'    => $dados
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao buscar as médias dessa disciplina.',
                'errors'  => $th->getMessage()
            ], 400);
        }
    }

    // Monta o ranking dos alunos com mais notas da disciplina
    public function ranking($uuid_disciplina)
    {
        try {
            // Busca a disciplina pelo uuid
            $disciplina = DisciplinaModel::where('uuid_disciplina', $uuid_disciplina)
                ->first();

            if (!$disciplina) {
                return response()->json([
                    'success' => false,
                    'message' => 'Disciplina não encontrada',
                    'data'    => ''
                ], 404);
            }

            // Busca as notas e os alunos em ordem decrescente
            $ranking = MatriculaModel::select('aluno.nome', DB::raw('SUM(pontuacao.nota) as nota_total'))
                ->join('aluno', 'matricula.codigo_aluno', 'aluno.codigo_aluno')
                ->join('pontuacao', 'aluno.codigo_aluno', 'pontuacao.codigo_aluno')
                ->where('matricula.codigo_disciplina', $disciplina->codigo_disciplina)
                ->groupBy('aluno.codigo_aluno')
                ->orderBy('nota_total', 'DESC')
                ->limit(10)
                ->get()->toArray();

            return response()->json([
                'success' => true,
                'message' => 'Ranking dos alunos',
                'data'    => $ranking
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao buscar o ranking dos alunos.',
                'errors'  => $th->getMessage()
            ], 400);
        }
    }
}
