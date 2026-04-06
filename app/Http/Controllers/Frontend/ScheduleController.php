<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\KbcRepository;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function __construct(private readonly KbcRepository $repository) {}

    public function index(): View
    {
        return view('frontend.schedules.index', [
            'schedules' => $this->repository->listSchedules(),
            'firebaseReady' => $this->repository->isFirebaseReady(),
            'firebaseError' => $this->repository->firebaseError(),
        ]);
    }
}
