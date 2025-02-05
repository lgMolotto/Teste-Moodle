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
        Schema::create('atividade', function (Blueprint $table) {
            $table->id('codigo_atividade');
            $table->uuid('uuid_atividade')->unique('uuid_atividade_unique');
            $table->unsignedBigInteger('codigo_disciplina')->nullable();
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->float('pontuacao_maxima');
            $table->char('status', 1);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('edited_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('codigo_disciplina', 'atividade_codigo_disciplina_fk')->references('codigo_disciplina')->on('disciplina');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atividade');
    }
};
