<?php

namespace App\Http\Controllers\Nosotros;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MisionController extends Controller
{
    public function mision()
    {
        return view('nosotros.mision');
    }
}
