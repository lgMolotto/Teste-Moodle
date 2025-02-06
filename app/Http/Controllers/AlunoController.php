<?php

namespace App\Http\Controllers;

use App\Http\Requests\AlunoRequest;
use App\Models\AlunoModel;
use App\Models\AtividadeModel;
use App\Models\MatriculaModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;

class AlunoController extends BaseController
{
    // Busca todos os alunos
    public function index()
    {
        try {
            $alunos = AlunoModel::get();

            return response()->json([
                'success' => true,
                'message' => 'Lista de alunos',
                'data'    => $alunos
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao buscar os alunos.',
                'errors'  => $th->getMessage()
            ], 500);
        }
    }

    // Busca um aluno específico pelo uuid dele
    public function show($uuid_aluno)
    {
        try {
            $aluno = AlunoModel::where('uuid_aluno', $uuid_aluno)
                ->first();

            // Verifica se encontrou o aluno
            if (!$aluno) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aluno não encontrado',
                    'data'    => ''
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Dados do aluno',
                'data'    => $aluno
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao buscar o aluno.',
                'errors'  => $th->getMessage()
            ], 500);
        }
    }

    // Salva um novo aluno
    public function store(AlunoRequest $request)
    {
        try {
            DB::beginTransaction();

            $aluno = AlunoModel::create($request->validated());

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Aluno cadastrado com sucesso!',
                'data'    => $aluno
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao cadastrar o aluno.',
                'errors'  => $th->getMessage()
            ], 500);
        }
    }

    // Atualiza um aluno
    public function update(AlunoRequest $request, $uuid_aluno)
    {
        try {
            DB::beginTransaction();

            $aluno = AlunoModel::where('uuid_aluno', $uuid_aluno)->first();

            if (!$aluno) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aluno não encontrado',
                    'data'    => ''
                ], 404);
            }

            $aluno->update($request->validated());

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Aluno editado com sucesso!',
                'data'    => ''
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao editar o aluno.',
                'errors'  => $th->getMessage()
            ], 500);
        }
    }

    // Inativa um aluno
    public function delete($uuid_aluno)
    {
        try {
            DB::beginTransaction();

            $aluno = AlunoModel::where('uuid_aluno', $uuid_aluno)->first();

            if (!$aluno) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aluno não encontrado',
                    'data'    => ''
                ], 404);
            }

            $aluno->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Aluno inativado com sucesso!',
                'data'    => ''
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao inativar o aluno.',
                'errors'  => $th->getMessage()
            ], 500);
        }
    }

    // Ativa um aluno
    public function restore($uuid_aluno)
    {
        try {
            DB::beginTransaction();

            $aluno = AlunoModel::withTrashed()
                ->where('uuid_aluno', $uuid_aluno)->first();

            if (!$aluno) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aluno não encontrado',
                    'data'    => ''
                ], 404);
            }

            $aluno->restore();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Aluno reativado com sucesso!',
                'data'    => ''
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao reativar o aluno.',
                'errors'  => $th->getMessage()
            ], 500);
        }
    }

    // Busca as atividades do aluno
    public function atividades($uuid_aluno)
    {
        try {
            // Busca o aluno pelo uuid
            $aluno = AlunoModel::where('uuid_aluno', $uuid_aluno)
                ->first();

            if (!$aluno) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aluno não encontrado',
                    'data'    => ''
                ], 404);
            }

            // Busca as disciplinas que esse aluno está matriculado
            $codigo_disciplinas = MatriculaModel::where('codigo_aluno', $aluno->codigo_aluno)
                ->pluck('codigo_disciplina');

            if ($codigo_disciplinas->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este aluno não está matriculado em menhuma disciplina',
                    'data'    => ''
                ], 404);
            }

            // Busca as atividades dessas disciplinas
            $atividades = AtividadeModel::whereIn('codigo_disciplina', $codigo_disciplinas)
                ->get();

            if (!$atividades) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma atividade encontrada para esse aluno',
                    'data'    => ''
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Atividades do aluno',
                'data'    => $atividades
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao buscar os alunos.',
                'errors'  => $th->getMessage()
            ], 500);
        }
    }

    // Busca as notas do aluno
    public function notas($uuid_aluno)
    {
        try {
            // Busca o aluno pelo uuid
            $aluno = AlunoModel::where('uuid_aluno', $uuid_aluno)
                ->first();

            if (!$aluno) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aluno não encontrado',
                    'data'    => ''
                ], 404);
            }

            // Busca as disciplinas que esse aluno está matriculado
            $disciplinas = MatriculaModel::select('disciplina.codigo_disciplina', 'disciplina.nome')
                ->where('codigo_aluno', $aluno->codigo_aluno)
                ->join('disciplina', 'matricula.codigo_disciplina', 'disciplina.codigo_disciplina')
                ->get();

            if (!$disciplinas) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este aluno não está matriculado em menhuma disciplina',
                    'data'    => ''
                ], 404);
            }

            foreach ($disciplinas as $key => $disciplina) {
                $dados[$key]['nome'] = $disciplina['nome'];

                // Busca a nota das atividades dessas disciplinas
                $atividades = AtividadeModel::select('atividade.titulo', 'pontuacao.nota')
                    ->join('pontuacao', 'atividade.codigo_atividade', 'pontuacao.codigo_atividade')
                    ->where('codigo_disciplina', $disciplina['codigo_disciplina'])
                    ->where('codigo_aluno', $aluno->codigo_aluno)
                    ->get()->toArray();

                if ($atividades) {
                    // Calcula a média da disciplina
                    $total_pontos = array_sum(array_column($atividades, 'nota'));
                    $media = round($total_pontos / count($atividades), 2);
                    $dados[$key]['total_pontos'] = $total_pontos;
                    $dados[$key]['media'] = $media;
                    $dados[$key]['atividades'] = $atividades;
                } else {
                    $dados[$key]['total_pontos'] = 0.00;
                    $dados[$key]['media'] = 0.00;
                    $dados[$key]['atividades'] = $atividades;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Notas do aluno',
                'data'    => $dados
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao buscar as notas do aluno.',
                'errors'  => $th->getMessage()
            ], 500);
        }
    }
}
