<?php

namespace App\Http\Controllers\Trainee;

use App\Http\Controllers\Controller;
use App\Services\CompetencyService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TraineeDigitalTwinController extends Controller
{
    public function __construct(
        protected CompetencyService $competencyService
    ) {}

    public function index(): View
    {
        $user = Auth::user();
        $twin = $this->competencyService->getDigitalTwinProfile($user);

        return view('trainee.digital-twin.index', compact('twin', 'user'));
    }
}
