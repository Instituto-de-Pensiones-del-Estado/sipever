<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableConsumos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consumos', function (Blueprint $table) {
            $table->increments('id_consumo');

            $table->integer('id_oficina')->unsigned();
            $table->foreign('id_oficina')->references('id')->on('cat_oficinas');

            $table->integer('id_poliza')->unsigned()->nullable();
            $table->foreign('id_poliza')->references('id_poliza')->on('polizas');

            $table->integer('id_periodo')->unsigned();
            $table->foreign('id_periodo')->references('id_periodo')->on('periodos');

            $table->integer('id_pedido_consumo')->unsigned();
            $table->foreign('id_pedido_consumo')->references('id_pedido_consumo')->on('c_pedido_consumo');

            $table->string('folio');
            $table->string('fecha_movimiento');
            $table->char('ubpp_consumo',4);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('consumos');
    }
}
