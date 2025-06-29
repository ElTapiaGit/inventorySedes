<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    //
    public function index()
    {
        return view('administrator.reportes');
    }
}
