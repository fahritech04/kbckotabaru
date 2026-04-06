<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\KbcRepository;
use Illuminate\Http\Request;
use RuntimeException;

class ScheduleController extends Controller
{
    public function __construct(private readonly KbcRepository $repository) {}

    public function index()
    {
        return view('admin.schedules.index', [
            'schedules' => $this->repository->listSchedules(),
            'firebaseReady' => $this->repository->isFirebaseReady(),
            'firebaseError' => $this->repository->firebaseError(),
        ]);
    }

    public function create()
    {
        return view('admin.schedules.create', [
            'tournaments' => $this->repository->listTournaments(),
        ]);
    }

    public function store(Request $request)
    {
        $payload = $this->validatePayload($request);

        try {
            $this->repository->createSchedule($payload);
        } catch (RuntimeException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $schedule = $this->repository->findSchedule($id);
        abort_if($schedule === null, 404);

        return view('admin.schedules.edit', [
            'schedule' => $schedule,
            'tournaments' => $this->repository->listTournaments(),
        ]);
    }

    public function update(Request $request, string $id)
    {
        $payload = $this->validatePayload($request);

        try {
            $this->repository->updateSchedule($id, $payload);
        } catch (RuntimeException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        try {
            $this->repository->deleteSchedule($id);
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil dihapus.');
    }

    private function validatePayload(Request $request): array
    {
        return $request->validate([
            'tournament_id' => ['required', 'string'],
            'title' => ['required', 'string', 'max:150'],
            'venue' => ['required', 'string', 'max:150'],
            'scheduled_at' => ['required', 'date'],
            'status' => ['required', 'in:draft,published,completed'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
