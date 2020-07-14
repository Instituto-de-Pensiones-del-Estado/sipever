<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;


class Polizas extends Model
{
    protected $connection = 'mysql2';
    protected $table = "polizas";
    protected $fillable = [
        'AÑO',
        'TIPOPOL',
        'NUMPOL',
        'NCONS',
        'CONCEPTO',
        'CLAVE',
        'CUENTAP',
        'SUBCTAP',
        'SSUBCTAP',
        'IMPORTE',
        'REFERENCIA',
        'DOCTO',
        'AFILIA',
        'FECH',
        'Sistema',
        'SIT'
    ];
}
