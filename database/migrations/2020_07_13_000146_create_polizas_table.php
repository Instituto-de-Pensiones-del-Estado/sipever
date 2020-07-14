<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePolizasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->create('polizas', function (Blueprint $table) {
            $table->integer('AÑO');
            $table->text('TIPOPOL');
            $table->integer('NUMPOL');
            $table->integer('NCONS');
            $table->text('CONCEPTO');
            $table->double('CLAVE');
            $table->integer('CUENTAP');
            $table->integer('SUBCTAP');
            $table->integer('SSUBCTAP');
            $table->double('IMPORTE');
            $table->text('REFERENCIA');
            $table->text('DOCTO');
            $table->text('AFILIA');
            $table->dateTime('FECH');
            $table->smallInteger('Sistema');
            $table->string('SIT',1);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql2')->dropIfExists('polizas');
    }
}
