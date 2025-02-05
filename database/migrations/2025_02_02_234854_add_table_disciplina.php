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
        Schema::create('disciplina', function (Blueprint $table) {
            $table->id('codigo_disciplina');
            $table->uuid('uuid_disciplina')->unique('uuid_disciplina_unique');
            $table->unsignedBigInteger('codigo_professor');
            $table->string('nome');
            $table->text('descricao');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('edited_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('codigo_professor', 'disciplina_codigo_professor_fk')->references('codigo_professor')->on('professor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disciplina');
    }
};
