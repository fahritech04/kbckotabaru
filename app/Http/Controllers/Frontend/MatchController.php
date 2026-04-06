<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\KbcRepository;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MatchController extends Controller
{
    public function __construct(private readonly KbcRepository $repository) {}

    public function index(Request $request): View
    {
        $statusFilter = $request->string('status')->toString();

        $matches = collect($this->repository->listMatches())
            ->when($statusFilter !== '', function ($collection) use ($statusFilter) {
                return $collection->where('status', $statusFilter);
            })
            ->values()
            ->all();

        return view('public-site.matches.index', [
            'matches' => $matches,
            'statusFilter' => $statusFilter,
            'firebaseReady' => $this->repository->isFirebaseReady(),
            'firebaseError' => $this->repository->firebaseError(),
        ]);
    }

    public function show(string $id): View
    {
        $match = $this->repository->findMatch($id);

        abort_if($match === null, 404);

        return view('public-site.matches.show', [
            'match' => $match,
        ]);
    }
}

