<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Services\CoordinatorScopeService;
use Illuminate\Http\Request;

class ReporteClinicaController extends Controller
{
    protected CoordinatorScopeService $scopeService;

    public function __construct(CoordinatorScopeService $scopeService)
    {
        $this->scopeService = $scopeService;
    }

    public function index()
    {
        $edificios = $this->scopeService->getEdificiosPermitidos();
        return view('coordinator.reporte', compact('edificios'));
    }

    public function indexClinica()
    {
        $edificios = $this->scopeService->getEdificiosPermitidos();
        return view('coordinator.reporte', compact('edificios'));
    }
}
