<?php

namespace App\Model\Catalogos;

use Illuminate\Database\Eloquent\Model;

class UnidadesAlmacen extends Model
{
    protected $table = 'cat_unidades_almacen';    
    protected $primaryKey = 'id';    
    protected $fillable = ['descripcion_corta', 'descripcion_larga', 'estatus'];

    public function setDescripcionAttribute($value)
    {
        $this->attributes['descripcion'] = mb_strtoupper($value,'utf-8');
    }

    public function setDescripcionLargaAttribute($value)
    {
        $this->attributes['descripcion_larga'] = mb_strtoupper($value,'utf-8');
    }
}
