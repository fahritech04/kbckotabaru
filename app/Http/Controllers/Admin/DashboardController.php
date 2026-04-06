<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\KbcRepository;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private readonly KbcRepository $repository) {}

    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'stats' => $this->repository->dashboardStats(),
            'latestMatches' => collect($this->repository->listMatches())->take(8)->all(),
            'latestSchedules' => collect($this->repository->listSchedules())->take(8)->all(),
            'firebaseReady' => $this->repository->isFirebaseReady(),
            'firebaseError' => $this->repository->firebaseError(),
        ]);
    }
}
