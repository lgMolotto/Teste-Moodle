<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pontuacao', function (Blueprint $table) {
            $table->id('codigo_pontuacao');
            $table->uuid('uuid_pontuacao')->unique('uuid_pontuacao_unique');
            $table->unsignedBigInteger('codigo_aluno');
            $table->unsignedBigInteger('codigo_atividade');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('edited_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('codigo_aluno', 'pontuacao_codigo_aluno_fk')->references('codigo_aluno')->on('aluno');
            $table->foreign('codigo_atividade', 'pontuacao_codigo_atividade_fk')->references('codigo_atividade')->on('atividade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pontuacao');
    }
};
