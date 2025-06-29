<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CoodinatorController extends BaseController   //se esta herendo al controlador BaseController.php
{
    //funcion para mandar la vista de la pagina principal coordinador
    public function inicio()
    {
        return view('coordinator.inicio');
    }

    public function laboratorios()
    {
        return view('coordinator.laboratorios');
    }

    //metodos para las clinicas
    public function home()
    {
        return view('coordinator.clinicas.home');
    }
}
