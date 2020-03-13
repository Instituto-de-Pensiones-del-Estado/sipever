<?php

namespace App\Model\Almacen;

use Illuminate\Database\Eloquent\Model;

class InvInicFinal extends Model
{
    protected $table = 'inventario_inicial_final';    
      
    protected $fillable = ['id_periodo', 'id_articulo', 'cant_inicial', 'existencias', 'precio_inicial', 'precio_promedio',
                            'estatus'];

                               
}


