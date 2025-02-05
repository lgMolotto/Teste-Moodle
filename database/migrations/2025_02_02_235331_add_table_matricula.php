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
        Schema::create('matricula', function (Blueprint $table) {
            $table->id('codigo_matricula');
            $table->uuid('uuid_matricula')->unique('uuid_matricula_unique');
            $table->unsignedBigInteger('codigo_aluno');
            $table->unsignedBigInteger('codigo_disciplina');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('edited_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('codigo_aluno', 'matricula_codigo_aluno_fk')->references('codigo_aluno')->on('aluno');
            $table->foreign('codigo_disciplina', 'matricula_codigo_disciplina_fk')->references('codigo_disciplina')->on('disciplina');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matricula');
    }
};
