<?php

namespace App\Http\Controllers\Encargado;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PrincipalController extends Controller
{
    //
    public function principal() {
        return view('encargados.index');
    }
}
