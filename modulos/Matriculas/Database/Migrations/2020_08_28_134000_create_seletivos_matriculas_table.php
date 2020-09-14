<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeletivosMatriculasTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seletivos_matriculas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('seletivo_user_id')->unsigned();
            $table->integer('chamada_id')->unsigned();
            $table->boolean('matriculado')->default(0);

            $table->timestamps();

            $table->foreign('seletivo_user_id')->references('id')->on('seletivos_users');
            $table->foreign('chamada_id')->references('id')->on('chamadas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('seletivos_matriculas');
    }
}