<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\RiskRadarService;
use Illuminate\View\View;

class AdminRiskRadarController extends Controller
{
    public function __construct(
        protected RiskRadarService $riskRadarService
    ) {}

    public function index(): View
    {
        $riskAnalysis = $this->riskRadarService->analyzeOrganizationalRisks();

        return view('admin.risk-radar.index', compact('riskAnalysis'));
    }
}
