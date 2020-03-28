<?php

namespace App\Model\Almacen;

use Illuminate\Database\Eloquent\Model;

class Periodo extends Model
{
    protected $table = 'periodos';    
    protected $primaryKey = 'id_periodo';    
    protected $fillable = [
    	'no_mes', 
    	'anio', 
        'estatus', 
    ];

    
}
