<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfessorRequest;
use App\Models\ProfessorModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;

class ProfessorController extends BaseController
{
    // Busca todos os professores
    public function index()
    {
        try {
            $professores = ProfessorModel::get();

            return response()->json([
                'success' => true,
                'message' => 'Lista de professores',
                'data' => $professores
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao buscar os professores.',
                'errors' => $th->getMessage()
            ], 500);
        }
    }

    // Busca um professor específico pelo uuid dele
    public function show($uuid_professor)
    {
        try {
            $professor = ProfessorModel::where('uuid_professor', $uuid_professor)
                ->first();

            // Verifica se encontrou o professor
            if (!$professor) {
                return response()->json([
                    'success' => true,
                    'message' => 'Professor não encontrado',
                    'data'    => ''
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Dados do professor',
                'data'    => $professor
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao buscar o professor.',
                'errors'  => $th->getMessage()
            ], 500);
        }
    }

    // Salva um novo professor
    public function store(ProfessorRequest $request)
    {
        try {
            DB::beginTransaction();

            $professor = ProfessorModel::create($request->validated());

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Professor cadastrado com sucesso!',
                'data'    => $professor
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao cadastrar o professor.',
                'errors' => $th->getMessage()
            ], 500);
        }
    }

    // Atualiza um professor
    public function update(ProfessorRequest $request, $uuid_professor)
    {
        try {
            DB::beginTransaction();

            $professor = ProfessorModel::where('uuid_professor', $uuid_professor)->first();

            if (!$professor) {
                return response()->json([
                    'success' => true,
                    'message' => 'Professor não encontrado',
                    'data'    => ''
                ], 404);
            }

            $professor->update($request->validated());

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Professor editado com sucesso!',
                'data'    => ''
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao editar o professor.',
                'errors' => $th->getMessage()
            ], 500);
        }
    }

    // Inativa um professor
    public function delete($uuid_professor)
    {
        try {
            DB::beginTransaction();

            $professor = ProfessorModel::where('uuid_professor', $uuid_professor)->first();

            if (!$professor) {
                return response()->json([
                    'success' => true,
                    'message' => 'Professor não encontrado',
                    'data'    => ''
                ], 404);
            }

            $professor->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Professor inativado com sucesso!',
                'data'    => ''
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao inativar o professor.',
                'errors' => $th->getMessage()
            ], 500);
        }
    }

    // Ativa um professor
    public function restore($uuid_professor)
    {
        try {
            DB::beginTransaction();

            $professor = ProfessorModel::withTrashed()
                ->where('uuid_professor', $uuid_professor)->first();

            if (!$professor) {
                return response()->json([
                    'success' => true,
                    'message' => 'Professor não encontrado',
                    'data'    => ''
                ], 404);
            }

            $professor->restore();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Professor reativado com sucesso!',
                'data'    => ''
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao reativar o professor.',
                'errors' => $th->getMessage()
            ], 500);
        }
    }

    // Busca as disciplinas de um professor
    public function disciplinas($uuid_professor)
    {
        try {
            $disciplinas = ProfessorModel::select('disciplina.*')
                ->where('uuid_professor', $uuid_professor)
                ->join('disciplina', 'professor.codigo_professor', 'disciplina.codigo_professor')
                ->get();

            // Verifica se encontrou o professor
            if (!$disciplinas) {
                return response()->json([
                    'success' => true,
                    'message' => 'Disciplinas do professor não encontradas',
                    'data'    => ''
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Disciplinas do professor',
                'data'    => $disciplinas
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao buscar as disciplinas do professor.',
                'errors' => $th->getMessage()
            ], 500);
        }
    }
}
