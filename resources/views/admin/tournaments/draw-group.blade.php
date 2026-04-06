@extends('layouts.admin-panel', ['title' => 'Drawing Grup'])

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Drawing Grup Live',
        'description' => 'Proses undian grup ditampilkan langkah demi langkah agar bisa disaksikan bersama.',
        'secondaryAction' => [
            'label' => 'Kembali',
            'url' => route('admin.tournaments.index'),
            'class' => 'btn-secondary',
        ],
    ])

    <section class="mt-6 surface-card p-5 sm:p-6">
        <div class="mb-5 rounded-xl border border-slate-200 bg-slate-50 p-4">
            <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Turnamen</div>
            <h2 class="mt-1 text-lg font-black text-slate-900">{{ $tournament['name'] ?? '-' }} ({{ $tournament['season'] ?? '-' }})</h2>
            <p class="mt-1 text-sm text-slate-600">Total Klub: {{ count($clubs) }} &middot; Total Grup: {{ $groupCount }}</p>
        </div>

        <div class="grid gap-5 xl:grid-cols-[320px,1fr]">
            <div class="rounded-xl border border-slate-200 p-4">
                <h3 class="text-sm font-black text-slate-900">Pot Klub Peserta</h3>
                <div class="mt-3 max-h-[460px] space-y-2 overflow-auto pr-1">
                    @foreach ($clubs as $club)
                        <div class="rounded-lg border border-slate-100 bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-700">
                            {{ $loop->iteration }}. {{ $club['name'] }}
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="space-y-5">
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Sedang Diundi</div>
                    <div id="drawing-current" class="mt-1 text-2xl font-black text-slate-900">Siap memulai drawing...</div>
                    <div id="drawing-progress" class="mt-2 text-sm text-slate-600">0 / {{ count($clubs) }} klub</div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($groupNames as $groupName)
                        <article class="rounded-xl border border-slate-200 p-4">
                            <h3 class="text-sm font-black text-slate-900">{{ $groupName }}</h3>
                            <ul data-group-list="{{ $groupName }}" class="mt-3 space-y-2">
                                <li class="rounded-lg border border-dashed border-slate-200 px-3 py-2 text-xs text-slate-400">Menunggu hasil drawing...</li>
                            </ul>
                        </article>
                    @endforeach
                </div>

                <div class="rounded-xl border border-slate-200 p-4">
                    <h3 class="text-sm font-black text-slate-900">Log Proses Drawing</h3>
                    <div id="drawing-log" class="mt-3 max-h-44 space-y-2 overflow-auto pr-1">
                        <div class="rounded-lg bg-slate-50 px-3 py-2 text-xs text-slate-500">Tekan tombol mulai drawing untuk menampilkan proses undian.</div>
                    </div>
                </div>
            </div>
        </div>

        <form id="draw-apply-form" method="POST" action="{{ route('admin.tournaments.draw-group.apply', $tournament['id']) }}" class="mt-6">
            @csrf
            <input id="group-draw-results-input" type="hidden" name="group_draw_results" />

            <div class="flex flex-col gap-2 sm:flex-row sm:justify-end">
                <button id="start-drawing-button" type="button" class="btn-secondary">Mulai Drawing</button>
                <button id="redraw-button" type="button" class="btn-secondary" disabled>Acak Ulang</button>
                <button id="save-drawing-button" type="submit" class="btn-primary" disabled>Tetapkan Hasil Drawing</button>
            </div>
        </form>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const teams = @json($clubs);
            const groupNames = @json($groupNames);

            const currentEl = document.getElementById('drawing-current');
            const progressEl = document.getElementById('drawing-progress');
            const logEl = document.getElementById('drawing-log');
            const resultInput = document.getElementById('group-draw-results-input');

            const startButton = document.getElementById('start-drawing-button');
            const redrawButton = document.getElementById('redraw-button');
            const saveButton = document.getElementById('save-drawing-button');

            let drawingInProgress = false;

            const getGroupListElement = (groupName) => document.querySelector(`[data-group-list="${groupName}"]`);

            const shuffle = (items) => {
                const arr = [...items];
                for (let i = arr.length - 1; i > 0; i -= 1) {
                    const j = Math.floor(Math.random() * (i + 1));
                    [arr[i], arr[j]] = [arr[j], arr[i]];
                }
                return arr;
            };

            const calculateCapacities = (teamCount, groupsCount) => {
                const capacities = [];
                const base = Math.floor(teamCount / groupsCount);
                const remainder = teamCount % groupsCount;

                for (let i = 0; i < groupsCount; i += 1) {
                    capacities.push(base + (i < remainder ? 1 : 0));
                }

                return capacities;
            };

            const buildGroupSequence = (teamCount, names) => {
                const capacities = calculateCapacities(teamCount, names.length);
                const sequence = [];

                names.forEach((groupName, groupIndex) => {
                    const slots = capacities[groupIndex] || 0;
                    for (let i = 0; i < slots; i += 1) {
                        sequence.push(groupName);
                    }
                });

                return sequence;
            };

            const appendLog = (text) => {
                const row = document.createElement('div');
                row.className = 'rounded-lg bg-slate-50 px-3 py-2 text-xs text-slate-700';
                row.textContent = text;
                logEl.prepend(row);
            };

            const resetBoard = () => {
                groupNames.forEach((groupName) => {
                    const list = getGroupListElement(groupName);
                    if (!list) {
                        return;
                    }

                    list.innerHTML = '';
                    const waiting = document.createElement('li');
                    waiting.className = 'rounded-lg border border-dashed border-slate-200 px-3 py-2 text-xs text-slate-400';
                    waiting.textContent = 'Menunggu hasil drawing...';
                    list.appendChild(waiting);
                });

                logEl.innerHTML = '';
                const info = document.createElement('div');
                info.className = 'rounded-lg bg-slate-50 px-3 py-2 text-xs text-slate-500';
                info.textContent = 'Proses drawing dimulai...';
                logEl.appendChild(info);

                currentEl.textContent = 'Mengacak peserta...';
                progressEl.textContent = `0 / ${teams.length} klub`;
                resultInput.value = '';
                saveButton.disabled = true;
            };

            const runDrawing = () => {
                if (drawingInProgress || teams.length === 0) {
                    return;
                }

                drawingInProgress = true;
                startButton.disabled = true;
                redrawButton.disabled = true;
                resetBoard();

                const shuffledTeams = shuffle(teams);
                const assignments = {};
                groupNames.forEach((groupName) => {
                    assignments[groupName] = [];
                });
                const groupSequence = buildGroupSequence(shuffledTeams.length, groupNames);

                let cursor = 0;
                const stepDuration = 900;

                const step = () => {
                    if (cursor >= shuffledTeams.length) {
                        drawingInProgress = false;
                        currentEl.textContent = 'Drawing selesai. Silakan tetapkan hasil.';
                        progressEl.textContent = `${teams.length} / ${teams.length} klub`;
                        resultInput.value = JSON.stringify(assignments);
                        saveButton.disabled = false;
                        startButton.disabled = true;
                        redrawButton.disabled = false;
                        appendLog('Drawing selesai dan siap disimpan.');
                        return;
                    }

                    const team = shuffledTeams[cursor];
                    const groupName = groupSequence[cursor] || groupNames[groupNames.length - 1];
                    assignments[groupName].push(team.id);

                    const list = getGroupListElement(groupName);
                    if (list) {
                        const waiting = list.querySelector('li.text-slate-400');
                        if (waiting) {
                            waiting.remove();
                        }

                        const row = document.createElement('li');
                        row.className = 'rounded-lg bg-slate-900 px-3 py-2 text-sm font-semibold text-white';
                        row.textContent = team.name;
                        list.appendChild(row);
                    }

                    currentEl.textContent = `${team.name} masuk ${groupName}`;
                    progressEl.textContent = `${cursor + 1} / ${teams.length} klub`;
                    appendLog(`Bola ${cursor + 1}: ${team.name} -> ${groupName}`);

                    cursor += 1;
                    window.setTimeout(step, stepDuration);
                };

                window.setTimeout(step, 500);
            };

            startButton.addEventListener('click', runDrawing);
            redrawButton.addEventListener('click', runDrawing);
        });
    </script>
@endsection
