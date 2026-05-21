<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $dashboardService) {}

    public function index(Request $request): View
    {
        $data = $this->dashboardService->getDashboardData(auth()->id());
        return view('dashboard.index', $data);
    }
}
