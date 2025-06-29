<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Inicioontroller extends Controller
{
    //
    public function inicioadmin(){
        return view('administrator.inicio');
    }
}
