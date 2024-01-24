<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('colaboradores', function (Blueprint $table) {
            $table->id();
            $table->string('matricula');
            $table->string('nome');
            $table->string('cpf');
            $table->string('telefone');
            $table->string('email');
            $table->string('usuario');
            $table->date('dt_nascimento');
            $table->date('dt_admissao');
            $table->unsignedBigInteger('cargo_id');
            $table->unsignedBigInteger('funcao_id');
            $table->date('dt_recisao')->nullable();
            $table->timestamps();
        });

        Schema::table('colaboradores', function(Blueprint $table) {
            $table->foreign('cargo_id')->references('id')->on('cargos');
            $table->foreign('funcao_id')->references('id')->on('funcoes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('colaboradores');
    }
};
