<?php

use App\Http\Controllers\AlunoController;
use App\Http\Controllers\AtividadeController;
use App\Http\Controllers\DisciplinaController;
use App\Http\Controllers\MatriculaController;
use App\Http\Controllers\ProfessorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Rotas de professor
Route::prefix('professores/')
    ->group(function () {
        Route::get('', [ProfessorController::class, 'index']);
        Route::post('store', [ProfessorController::class, 'store']);
        Route::get('{uuid}/show', [ProfessorController::class, 'show']);
        Route::put('{uuid}/update', [ProfessorController::class, 'update']);
        Route::delete('{uuid}/delete', [ProfessorController::class, 'delete']);
        Route::patch('{uuid}/restore', [ProfessorController::class, 'restore']);

        Route::get('{uuid}/disciplinas', [ProfessorController::class, 'disciplinas']);
    });


// Rotas de disciplina
Route::prefix('disciplinas/')
    ->group(function () {
        Route::get('', [DisciplinaController::class, 'index']);
        Route::post('store', [DisciplinaController::class, 'store']);
        Route::get('{uuid}/show', [DisciplinaController::class, 'show']);
        Route::put('{uuid}/update', [DisciplinaController::class, 'update']);
        Route::delete('{uuid}/delete', [DisciplinaController::class, 'delete']);
        Route::patch('{uuid}/restore', [DisciplinaController::class, 'restore']);

        Route::get('{uuid}/atividades', [DisciplinaController::class, 'atividades']);
        Route::get('{uuid}/medias', [DisciplinaController::class, 'medias']);
        Route::get('{uuid}/ranking', [DisciplinaController::class, 'ranking']);
    });

// Rotas de atividade
Route::prefix('atividades/')
    ->group(function () {
        Route::get('', [AtividadeController::class, 'index']);
        Route::post('store', [AtividadeController::class, 'store']);
        Route::get('{uuid}/show', [AtividadeController::class, 'show']);
        Route::put('{uuid}/update', [AtividadeController::class, 'update']);
        Route::delete('{uuid}/delete', [AtividadeController::class, 'delete']);
        Route::patch('{uuid}/restore', [AtividadeController::class, 'restore']);

        Route::patch('{uuid}/finalizar', [AtividadeController::class, 'finalizar']);
        Route::post('avaliar-aluno', [AtividadeController::class, 'avaliarAluno']);
    });

// Rotas de aluno
Route::prefix('alunos/')
    ->group(function () {
        Route::get('', [AlunoController::class, 'index']);
        Route::post('store', [AlunoController::class, 'store']);
        Route::get('{uuid}/show', [AlunoController::class, 'show']);
        Route::put('{uuid}/update', [AlunoController::class, 'update']);
        Route::delete('{uuid}/delete', [AlunoController::class, 'delete']);
        Route::patch('{uuid}/restore', [AlunoController::class, 'restore']);

        Route::get('{uuid}/atividades', [AlunoController::class, 'atividades']);
        Route::get('{uuid}/notas', [AlunoController::class, 'notas']);
    });

// Rotas de matriculas
Route::prefix('matriculas/')
    ->group(function () {
        Route::post('store', [MatriculaController::class, 'store']);
        Route::delete('{uuid}/delete', [MatriculaController::class, 'delete']);
        Route::patch('{uuid}/restore', [MatriculaController::class, 'restore']);
    });
