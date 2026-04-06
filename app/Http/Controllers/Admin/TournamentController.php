<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ImageUploadService;
use App\Services\KbcRepository;
use App\Services\TournamentAutomationService;
use App\Services\TournamentSystemService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class TournamentController extends Controller
{
    public function __construct(
        private readonly KbcRepository $repository,
        private readonly TournamentSystemService $systemService,
        private readonly TournamentAutomationService $automationService,
        private readonly ImageUploadService $imageUploadService
    ) {}

    public function index()
    {
        return view('admin.tournaments.index', [
            'tournaments' => $this->repository->listTournaments(),
            'systemOptions' => $this->systemService->systemOptions(),
            'firebaseReady' => $this->repository->isFirebaseReady(),
            'firebaseError' => $this->repository->firebaseError(),
        ]);
    }

    public function create()
    {
        return view('admin.tournaments.create', [
            'systemOptions' => $this->systemService->systemOptions(),
            'systemFieldVisibilityMap' => $this->systemService->systemSettingVisibilityMap(),
            'systemFieldRequiredMap' => $this->systemService->systemSettingRequiredMap(),
        ]);
    }

    public function store(Request $request)
    {
        $payload = $this->validatePayload($request);
        $payload['hero_image'] = $this->imageUploadService->store(
            $request->file('hero_image_file'),
            'tournaments/heroes'
        ) ?? ($payload['hero_image'] ?? null);
        unset($payload['hero_image_file']);
        $payload['slug'] = Str::slug($payload['name'].'-'.$payload['season']);
        $shouldAutoSync = ((int) ($payload['auto_sync_system'] ?? 1)) === 1;
        unset($payload['auto_sync_system']);

        try {
            $createdTournament = $this->repository->createTournament($payload);

            if ($shouldAutoSync) {
                $this->automationService->syncTournament($createdTournament['id']);
            }
        } catch (RuntimeException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        return redirect()->route('admin.tournaments.index')->with('success', $shouldAutoSync
            ? 'Turnamen berhasil ditambahkan dan sistem pertandingan otomatis disiapkan.'
            : 'Turnamen berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $tournament = $this->repository->findTournament($id);
        abort_if($tournament === null, 404);

        return view('admin.tournaments.edit', [
            'tournament' => $tournament,
            'systemOptions' => $this->systemService->systemOptions(),
            'systemFieldVisibilityMap' => $this->systemService->systemSettingVisibilityMap(),
            'systemFieldRequiredMap' => $this->systemService->systemSettingRequiredMap(),
        ]);
    }

    public function update(Request $request, string $id)
    {
        $tournament = $this->repository->findTournament($id);
        abort_if($tournament === null, 404);

        $payload = $this->validatePayload($request);
        $payload['hero_image'] = $this->imageUploadService->store(
            $request->file('hero_image_file'),
            'tournaments/heroes',
            $tournament['hero_image'] ?? null
        ) ?? ($payload['hero_image'] ?? ($tournament['hero_image'] ?? null));
        unset($payload['hero_image_file']);
        $payload['slug'] = Str::slug($payload['name'].'-'.$payload['season']);
        $shouldAutoSync = ((int) ($payload['auto_sync_system'] ?? 1)) === 1;
        unset($payload['auto_sync_system']);

        try {
            $this->repository->updateTournament($id, $payload);

            if ($shouldAutoSync) {
                $this->automationService->syncTournament($id);
            }
        } catch (RuntimeException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        return redirect()->route('admin.tournaments.index')->with('success', $shouldAutoSync
            ? 'Turnamen berhasil diperbarui dan sistem pertandingan disinkronkan.'
            : 'Turnamen berhasil diperbarui.');
    }

    public function sync(string $id): RedirectResponse
    {
        try {
            $result = $this->automationService->syncTournament($id);
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with(
            'success',
            "Sinkronisasi sistem {$result['system_label']} selesai: {$result['created_matches']} pertandingan & {$result['created_schedules']} jadwal dibuat."
        );
    }

    public function destroy(string $id)
    {
        try {
            $this->repository->deleteTournament($id);
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return redirect()->route('admin.tournaments.index')->with('success', 'Turnamen berhasil dihapus.');
    }

    private function validatePayload(Request $request): array
    {
        $payload = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'season' => ['required', 'string', 'max:30'],
            'location' => ['required', 'string', 'max:120'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'in:upcoming,ongoing,finished'],
            'description' => ['nullable', 'string'],
            'hero_image' => ['nullable', 'string', 'max:255'],
            'hero_image_file' => ['nullable', 'image', 'max:4096'],
            'competition_system' => ['required', 'in:'.implode(',', $this->systemService->validSystemCodes())],
            'system_rounds' => ['nullable', 'integer', 'min:1', 'max:24'],
            'system_group_count' => ['nullable', 'integer', 'min:2', 'max:8'],
            'system_qualifiers_per_group' => ['nullable', 'integer', 'min:1', 'max:8'],
            'system_best_of' => ['nullable', 'integer', 'in:3,5,7'],
            'system_play_in_slots' => ['nullable', 'integer', 'min:2', 'max:16'],
            'auto_sync_system' => ['nullable', 'boolean'],
        ]);

        $systemCode = $this->systemService->normalizeSystemCode($payload['competition_system'] ?? null);
        $this->validateSystemSpecificFields($systemCode, $payload);

        foreach (array_keys($this->systemService->settingFieldLabels()) as $fieldKey) {
            if (! in_array($fieldKey, $this->systemService->visibleSettingFields($systemCode), true)) {
                $payload[$fieldKey] = null;
            }
        }

        $payload['competition_system'] = $systemCode;
        $payload['competition_system_label'] = $this->systemService->systemLabel($systemCode);
        $payload['competition_settings'] = $this->systemService->normalizeSettings($systemCode, [
            'system_rounds' => $payload['system_rounds'] ?? null,
            'system_group_count' => $payload['system_group_count'] ?? null,
            'system_qualifiers_per_group' => $payload['system_qualifiers_per_group'] ?? null,
            'system_best_of' => $payload['system_best_of'] ?? null,
            'system_play_in_slots' => $payload['system_play_in_slots'] ?? null,
        ]);
        $payload['standings_enabled'] = $this->systemService->supportsStandings($systemCode);

        unset(
            $payload['system_rounds'],
            $payload['system_group_count'],
            $payload['system_qualifiers_per_group'],
            $payload['system_best_of'],
            $payload['system_play_in_slots']
        );

        return $payload;
    }

    private function validateSystemSpecificFields(string $systemCode, array $payload): void
    {
        $requiredFields = $this->systemService->requiredSettingFields($systemCode);

        if ($requiredFields === []) {
            return;
        }

        $messages = [];

        foreach ($requiredFields as $fieldKey) {
            $value = $payload[$fieldKey] ?? null;
            if ($value === null || $value === '') {
                $messages[$fieldKey] = $this->systemService->settingFieldLabel($fieldKey)
                    .' wajib diisi untuk sistem '.$this->systemService->systemLabel($systemCode).'.';
            }
        }

        if ($messages !== []) {
            throw ValidationException::withMessages($messages);
        }
    }
}
