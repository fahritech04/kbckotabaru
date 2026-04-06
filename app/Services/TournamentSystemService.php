<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Str;

class TournamentSystemService
{
    public const SYSTEM_SINGLE_ELIMINATION = 'single_elimination';

    public const SYSTEM_DOUBLE_ELIMINATION = 'double_elimination';

    public const SYSTEM_SINGLE_ROUND_ROBIN = 'single_round_robin';

    public const SYSTEM_DOUBLE_ROUND_ROBIN = 'double_round_robin';

    public const SYSTEM_GROUP_KNOCKOUT = 'group_knockout';

    public const SYSTEM_SWISS = 'swiss';

    public const SYSTEM_BEST_OF_SERIES = 'best_of_series';

    public const SYSTEM_PLAY_IN = 'play_in';

    public const SYSTEM_CONSOLATION = 'consolation_bracket';

    public const SYSTEM_JAMBOREE = 'jamboree';

    public const SYSTEM_FIBA_3X3 = 'fiba_3x3';

    public const SYSTEM_LADDER = 'ladder';

    public const SYSTEM_DRAFT_LEAGUE = 'draft_league';

    private const SYSTEM_DEFINITIONS = [
        self::SYSTEM_SINGLE_ELIMINATION => [
            'label' => 'Sistem Gugur (Single Elimination)',
            'description' => 'Kalah langsung tersingkir hingga tersisa juara.',
            'supports_standings' => false,
        ],
        self::SYSTEM_DOUBLE_ELIMINATION => [
            'label' => 'Sistem Gugur Ganda (Double Elimination)',
            'description' => 'Tim tersingkir setelah dua kali kalah (winner + loser bracket).',
            'supports_standings' => false,
        ],
        self::SYSTEM_SINGLE_ROUND_ROBIN => [
            'label' => 'Sistem Setengah Kompetisi (Single Round Robin)',
            'description' => 'Semua tim saling bertemu satu kali.',
            'supports_standings' => true,
        ],
        self::SYSTEM_DOUBLE_ROUND_ROBIN => [
            'label' => 'Sistem Kompetisi Penuh (Double Round Robin)',
            'description' => 'Semua tim saling bertemu dua kali (home-away).',
            'supports_standings' => true,
        ],
        self::SYSTEM_GROUP_KNOCKOUT => [
            'label' => 'Sistem Kombinasi / Campuran (Group + Knockout)',
            'description' => 'Fase grup lalu lanjut gugur.',
            'supports_standings' => true,
        ],
        self::SYSTEM_SWISS => [
            'label' => 'Sistem Swiss (Swiss System)',
            'description' => 'Pasangan pertandingan berdasarkan rekor poin yang mirip.',
            'supports_standings' => true,
        ],
        self::SYSTEM_BEST_OF_SERIES => [
            'label' => 'Sistem Seri (Best-of Series)',
            'description' => 'Dua tim bermain beberapa game sampai capai mayoritas kemenangan.',
            'supports_standings' => false,
        ],
        self::SYSTEM_PLAY_IN => [
            'label' => 'Turnamen Play-In',
            'description' => 'Babak tambahan sebelum fase utama.',
            'supports_standings' => false,
        ],
        self::SYSTEM_CONSOLATION => [
            'label' => 'Sistem Gugur + Penentuan Peringkat (Consolation)',
            'description' => 'Tim kalah tetap bermain untuk perebutan peringkat.',
            'supports_standings' => false,
        ],
        self::SYSTEM_JAMBOREE => [
            'label' => 'Sistem Ekshibisi / Showcase (Jamboree)',
            'description' => 'Pertandingan uji coba/pameran tanpa gelar resmi.',
            'supports_standings' => false,
        ],
        self::SYSTEM_FIBA_3X3 => [
            'label' => 'Format Spesifik 3x3 (FIBA 3x3 Rules)',
            'description' => 'Format 3x3 setengah lapangan.',
            'supports_standings' => true,
        ],
        self::SYSTEM_LADDER => [
            'label' => 'Sistem Tangga (Ladder / Pyramid)',
            'description' => 'Tim bawah menantang tim di peringkat atasnya.',
            'supports_standings' => true,
        ],
        self::SYSTEM_DRAFT_LEAGUE => [
            'label' => 'Liga Cabutan (Draft / Pick-up League)',
            'description' => 'Kompetisi berbasis draft/pengacakan pemain.',
            'supports_standings' => true,
        ],
    ];

    private const SETTING_FIELDS = [
        'system_rounds' => [
            'label' => 'Jumlah Ronde Sistem',
            'visible_in' => [self::SYSTEM_SWISS, self::SYSTEM_LADDER],
            'required_in' => [self::SYSTEM_SWISS, self::SYSTEM_LADDER],
        ],
        'system_best_of' => [
            'label' => 'Best Of (Series)',
            'visible_in' => [self::SYSTEM_BEST_OF_SERIES],
            'required_in' => [self::SYSTEM_BEST_OF_SERIES],
        ],
        'system_group_count' => [
            'label' => 'Jumlah Grup',
            'visible_in' => [self::SYSTEM_GROUP_KNOCKOUT],
            'required_in' => [self::SYSTEM_GROUP_KNOCKOUT],
        ],
        'system_qualifiers_per_group' => [
            'label' => 'Lolos per Grup',
            'visible_in' => [self::SYSTEM_GROUP_KNOCKOUT],
            'required_in' => [self::SYSTEM_GROUP_KNOCKOUT],
        ],
        'system_play_in_slots' => [
            'label' => 'Slot Tim Play-In',
            'visible_in' => [self::SYSTEM_PLAY_IN],
            'required_in' => [self::SYSTEM_PLAY_IN],
        ],
    ];

    public function systemOptions(): array
    {
        $options = [];

        foreach (self::SYSTEM_DEFINITIONS as $key => $definition) {
            $options[] = [
                'code' => $key,
                'label' => $definition['label'],
                'description' => $definition['description'],
                'supports_standings' => (bool) ($definition['supports_standings'] ?? false),
            ];
        }

        return $options;
    }

    public function validSystemCodes(): array
    {
        return array_keys(self::SYSTEM_DEFINITIONS);
    }

    public function settingFieldLabels(): array
    {
        $labels = [];

        foreach (self::SETTING_FIELDS as $key => $config) {
            $labels[$key] = $config['label'] ?? $key;
        }

        return $labels;
    }

    public function settingFieldLabel(string $fieldKey): string
    {
        return self::SETTING_FIELDS[$fieldKey]['label'] ?? $fieldKey;
    }

    public function visibleSettingFields(string $systemCode): array
    {
        $code = $this->normalizeSystemCode($systemCode);
        $fields = [];

        foreach (self::SETTING_FIELDS as $key => $config) {
            if (in_array($code, $config['visible_in'] ?? [], true)) {
                $fields[] = $key;
            }
        }

        return $fields;
    }

    public function requiredSettingFields(string $systemCode): array
    {
        $code = $this->normalizeSystemCode($systemCode);
        $fields = [];

        foreach (self::SETTING_FIELDS as $key => $config) {
            if (in_array($code, $config['required_in'] ?? [], true)) {
                $fields[] = $key;
            }
        }

        return $fields;
    }

    public function systemSettingVisibilityMap(): array
    {
        $map = [];

        foreach ($this->validSystemCodes() as $code) {
            $map[$code] = $this->visibleSettingFields($code);
        }

        return $map;
    }

    public function systemSettingRequiredMap(): array
    {
        $map = [];

        foreach ($this->validSystemCodes() as $code) {
            $map[$code] = $this->requiredSettingFields($code);
        }

        return $map;
    }

    public function normalizeSystemCode(?string $systemCode): string
    {
        $code = trim((string) $systemCode);

        if ($code === '' || ! array_key_exists($code, self::SYSTEM_DEFINITIONS)) {
            return self::SYSTEM_SINGLE_ELIMINATION;
        }

        return $code;
    }

    public function systemLabel(?string $systemCode): string
    {
        $code = $this->normalizeSystemCode($systemCode);

        return self::SYSTEM_DEFINITIONS[$code]['label'];
    }

    public function systemDescription(?string $systemCode): string
    {
        $code = $this->normalizeSystemCode($systemCode);

        return self::SYSTEM_DEFINITIONS[$code]['description'];
    }

    public function supportsStandings(?string $systemCode): bool
    {
        $code = $this->normalizeSystemCode($systemCode);

        return (bool) (self::SYSTEM_DEFINITIONS[$code]['supports_standings'] ?? false);
    }

    public function normalizeSettings(string $systemCode, array $settings, int $clubCount = 0): array
    {
        $code = $this->normalizeSystemCode($systemCode);
        $visibleFields = $this->visibleSettingFields($code);
        $normalized = [];

        if (in_array('system_rounds', $visibleFields, true)) {
            $normalized['rounds'] = $this->clampInt((int) ($settings['rounds'] ?? $settings['system_rounds'] ?? 3), 1, 24);
        }
        if (in_array('system_group_count', $visibleFields, true)) {
            $normalized['group_count'] = $this->clampInt((int) ($settings['group_count'] ?? $settings['system_group_count'] ?? 2), 2, 8);
        }
        if (in_array('system_qualifiers_per_group', $visibleFields, true)) {
            $normalized['qualifiers_per_group'] = $this->clampInt((int) ($settings['qualifiers_per_group'] ?? $settings['system_qualifiers_per_group'] ?? 2), 1, 8);
        }
        if (in_array('system_best_of', $visibleFields, true)) {
            $normalized['best_of'] = $this->normalizeBestOf((int) ($settings['best_of'] ?? $settings['system_best_of'] ?? 3));
        }
        if (in_array('system_play_in_slots', $visibleFields, true)) {
            $normalized['play_in_slots'] = $this->clampInt((int) ($settings['play_in_slots'] ?? $settings['system_play_in_slots'] ?? 4), 2, 16);
        }

        if ($clubCount > 0) {
            if (array_key_exists('group_count', $normalized)) {
                $normalized['group_count'] = min($normalized['group_count'], max(2, $clubCount));
            }
            if (array_key_exists('qualifiers_per_group', $normalized)) {
                $groups = max(1, (int) ($normalized['group_count'] ?? 2));
                $normalized['qualifiers_per_group'] = min($normalized['qualifiers_per_group'], max(1, (int) ceil($clubCount / $groups)));
            }
            if (array_key_exists('play_in_slots', $normalized)) {
                $normalized['play_in_slots'] = min($normalized['play_in_slots'], $clubCount);
            }
        }

        $normalized['system'] = $code;

        return $normalized;
    }

    public function generatePlan(array $tournament, array $clubs, array $options = []): array
    {
        $systemCode = $this->normalizeSystemCode($tournament['competition_system'] ?? null);
        $settings = $this->normalizeSettings($systemCode, (array) ($tournament['competition_settings'] ?? []), count($clubs));
        $clubIds = $this->seedClubIds($clubs);
        $groupDrawResults = [];
        $forceGroupRedraw = (bool) ($options['force_group_redraw'] ?? false);

        if ($systemCode === self::SYSTEM_GROUP_KNOCKOUT) {
            $groupCount = min(max(2, (int) ($settings['group_count'] ?? 2)), max(1, count($clubIds)));
            $groupDrawResults = $this->resolveGroupDrawResults(
                $clubIds,
                $groupCount,
                (array) ($tournament['group_draw_results'] ?? []),
                $forceGroupRedraw
            );
        }

        $rounds = match ($systemCode) {
            self::SYSTEM_DOUBLE_ELIMINATION => $this->buildDoubleEliminationRounds($clubIds),
            self::SYSTEM_SINGLE_ROUND_ROBIN => $this->buildRoundRobinRounds($clubIds, false, 'Liga'),
            self::SYSTEM_DOUBLE_ROUND_ROBIN => $this->buildRoundRobinRounds($clubIds, true, 'Liga'),
            self::SYSTEM_GROUP_KNOCKOUT => $this->buildGroupKnockoutRounds($groupDrawResults, $settings),
            self::SYSTEM_SWISS => $this->buildSwissRounds($clubIds, $settings),
            self::SYSTEM_BEST_OF_SERIES => $this->buildBestOfSeriesRounds($clubIds, $settings),
            self::SYSTEM_PLAY_IN => $this->buildPlayInRounds($clubIds, $settings),
            self::SYSTEM_CONSOLATION => $this->buildConsolationRounds($clubIds),
            self::SYSTEM_JAMBOREE => $this->buildJamboreeRounds($clubIds),
            self::SYSTEM_FIBA_3X3 => $this->buildRoundRobinRounds($clubIds, false, '3x3', true),
            self::SYSTEM_LADDER => $this->buildLadderRounds($clubIds, $settings),
            self::SYSTEM_DRAFT_LEAGUE => $this->buildDraftLeagueRounds($clubIds),
            default => $this->buildSingleEliminationRounds($clubIds),
        };

        return $this->assemblePlan($rounds, $tournament, $systemCode, $settings, $groupDrawResults);
    }

    public function calculateStandings(array $tournament, array $clubs, array $matches): array
    {
        $systemCode = $this->normalizeSystemCode($tournament['competition_system'] ?? null);

        if (! $this->supportsStandings($systemCode)) {
            return [
                'enabled' => false,
                'title' => 'Klasemen',
                'tables' => [],
            ];
        }

        $clubIndex = collect($clubs)->keyBy('id');

        if ($clubIndex->isEmpty()) {
            return [
                'enabled' => true,
                'title' => 'Klasemen',
                'tables' => [],
            ];
        }

        $tournamentMatches = collect($matches)
            ->filter(fn (array $match): bool => ($match['tournament_id'] ?? null) === ($tournament['id'] ?? null))
            ->filter(fn (array $match): bool => ($match['status'] ?? 'scheduled') === 'selesai')
            ->filter(fn (array $match): bool => ($match['affects_standings'] ?? true) === true);

        if ($systemCode === self::SYSTEM_GROUP_KNOCKOUT) {
            $groupDrawResults = $this->normalizeGroupDrawResults((array) ($tournament['group_draw_results'] ?? []));

            if ($groupDrawResults === []) {
                $groupDrawResults = collect($matches)
                    ->filter(fn (array $match): bool => ($match['tournament_id'] ?? null) === ($tournament['id'] ?? null))
                    ->filter(fn (array $match): bool => trim((string) ($match['group'] ?? '')) !== '')
                    ->groupBy(fn (array $match): string => trim((string) ($match['group'] ?? '')))
                    ->map(function ($groupMatches): array {
                        return collect($groupMatches)
                            ->flatMap(fn (array $match): array => [
                                (string) ($match['home_club_id'] ?? ''),
                                (string) ($match['away_club_id'] ?? ''),
                            ])
                            ->filter(fn (string $clubId): bool => $clubId !== '')
                            ->unique()
                            ->values()
                            ->all();
                    })
                    ->all();
            }

            if ($groupDrawResults !== []) {
                $tables = [];

                foreach ($groupDrawResults as $groupName => $groupClubIds) {
                    $groupClubIndex = collect($groupClubIds)
                        ->mapWithKeys(fn (string $clubId): array => [$clubId => $clubIndex->get($clubId)])
                        ->filter();

                    if ($groupClubIndex->isEmpty()) {
                        continue;
                    }

                    $groupMatches = $tournamentMatches
                        ->filter(fn (array $match): bool => trim((string) ($match['group'] ?? '')) === (string) $groupName)
                        ->values()
                        ->all();

                    $tables[] = [
                        'name' => $groupName,
                        'rows' => $this->calculateStandingRows($groupClubIndex, $groupMatches, $systemCode),
                    ];
                }

                if ($tables !== []) {
                    return [
                        'enabled' => true,
                        'title' => 'Klasemen Fase Grup',
                        'tables' => $tables,
                    ];
                }
            }
        }

        return [
            'enabled' => true,
            'title' => 'Klasemen',
            'tables' => [
                [
                    'name' => 'Overall',
                    'rows' => $this->calculateStandingRows($clubIndex, $tournamentMatches->all(), $systemCode),
                ],
            ],
        ];
    }

    private function calculateStandingRows($clubIndex, array $matches, string $systemCode): array
    {
        $rows = [];

        foreach ($clubIndex as $clubId => $club) {
            $rows[$clubId] = [
                'club_id' => $clubId,
                'club_name' => $club['name'] ?? 'Klub',
                'played' => 0,
                'win' => 0,
                'draw' => 0,
                'loss' => 0,
                'points_for' => 0,
                'points_against' => 0,
                'diff' => 0,
                'league_points' => 0,
            ];
        }

        foreach ($matches as $match) {
            $homeId = $match['home_club_id'] ?? null;
            $awayId = $match['away_club_id'] ?? null;

            if ($homeId === null || $awayId === null || ! isset($rows[$homeId]) || ! isset($rows[$awayId])) {
                continue;
            }

            $homeScore = (int) ($match['home_score'] ?? 0);
            $awayScore = (int) ($match['away_score'] ?? 0);

            $rows[$homeId]['played']++;
            $rows[$awayId]['played']++;
            $rows[$homeId]['points_for'] += $homeScore;
            $rows[$homeId]['points_against'] += $awayScore;
            $rows[$awayId]['points_for'] += $awayScore;
            $rows[$awayId]['points_against'] += $homeScore;

            if ($homeScore === $awayScore) {
                $rows[$homeId]['draw']++;
                $rows[$awayId]['draw']++;
                $rows[$homeId]['league_points'] += 1;
                $rows[$awayId]['league_points'] += 1;

                continue;
            }

            $homeWinPoints = $systemCode === self::SYSTEM_FIBA_3X3 ? 2 : 2;
            $awayLosePoints = $systemCode === self::SYSTEM_FIBA_3X3 ? 1 : 0;

            if ($homeScore > $awayScore) {
                $rows[$homeId]['win']++;
                $rows[$awayId]['loss']++;
                $rows[$homeId]['league_points'] += $homeWinPoints;
                $rows[$awayId]['league_points'] += $awayLosePoints;
            } else {
                $rows[$awayId]['win']++;
                $rows[$homeId]['loss']++;
                $rows[$awayId]['league_points'] += $homeWinPoints;
                $rows[$homeId]['league_points'] += $awayLosePoints;
            }
        }

        $finalRows = array_values(array_map(function (array $row): array {
            $row['diff'] = $row['points_for'] - $row['points_against'];

            return $row;
        }, $rows));

        usort($finalRows, function (array $left, array $right): int {
            return [$right['league_points'], $right['diff'], $right['points_for'], $left['club_name']]
                <=> [$left['league_points'], $left['diff'], $left['points_for'], $right['club_name']];
        });

        foreach ($finalRows as $index => &$row) {
            $row['rank'] = $index + 1;
        }

        return $finalRows;
    }

    private function assemblePlan(
        array $rounds,
        array $tournament,
        string $systemCode,
        array $settings,
        array $groupDrawResults = []
    ): array {
        $startDate = ! empty($tournament['start_date'])
            ? Carbon::parse($tournament['start_date'])->startOfDay()->setHour(9)
            : now()->startOfDay()->setHour(9);
        $venue = $tournament['location'] ?? 'Kotabaru';
        $systemLabel = $this->systemLabel($systemCode);

        $schedules = [];
        $matches = [];

        foreach (array_values($rounds) as $roundIndex => $round) {
            $roundMatches = (array) ($round['matches'] ?? []);
            if ($roundMatches === []) {
                continue;
            }

            $scheduleRef = 'round_'.($roundIndex + 1);
            $roundTime = $startDate->copy()->addDays($roundIndex);
            $roundName = (string) ($round['name'] ?? ('Round '.($roundIndex + 1)));
            $stageName = (string) ($round['stage'] ?? 'main');

            $schedules[] = [
                'reference' => $scheduleRef,
                'title' => "{$tournament['name']} - {$roundName}",
                'venue' => $venue,
                'scheduled_at' => $roundTime->toIso8601String(),
                'status' => 'published',
                'notes' => "{$systemLabel} | Stage: {$stageName}",
                'generated_by_system' => true,
                'competition_system' => $systemCode,
                'stage' => $stageName,
            ];

            foreach (array_values($roundMatches) as $matchIndex => $fixture) {
                $tipoff = $roundTime->copy()->addHours($matchIndex * 2);

                $matches[] = [
                    'schedule_reference' => $scheduleRef,
                    'home_club_id' => $fixture['home_club_id'] ?? null,
                    'away_club_id' => $fixture['away_club_id'] ?? null,
                    'round' => $roundName,
                    'tipoff_at' => $tipoff->toIso8601String(),
                    'venue' => $venue,
                    'status' => 'scheduled',
                    'home_score' => 0,
                    'away_score' => 0,
                    'highlight' => null,
                    'generated_by_system' => true,
                    'competition_system' => $systemCode,
                    'stage' => $stageName,
                    'bracket' => $round['bracket'] ?? 'main',
                    'group' => $fixture['group'] ?? ($round['group'] ?? null),
                    'affects_standings' => (bool) ($round['affects_standings'] ?? false),
                    'best_of' => $fixture['best_of'] ?? null,
                    'series_key' => $fixture['series_key'] ?? null,
                    'match_type' => $fixture['match_type'] ?? ($round['match_type'] ?? '5x5'),
                    'ruleset' => $fixture['ruleset'] ?? null,
                ];
            }
        }

        return [
            'system_code' => $systemCode,
            'system_label' => $systemLabel,
            'supports_standings' => $this->supportsStandings($systemCode),
            'settings' => $settings,
            'group_draw_results' => $groupDrawResults,
            'rounds' => $rounds,
            'schedules' => $schedules,
            'matches' => $matches,
        ];
    }

    private function buildSingleEliminationRounds(array $clubIds): array
    {
        if ($clubIds === []) {
            return [];
        }

        $roundCount = (int) ceil(log(max(2, count($clubIds)), 2));
        $bracketSize = (int) pow(2, $roundCount);
        $seeded = array_values($clubIds);

        while (count($seeded) < $bracketSize) {
            $seeded[] = null;
        }

        $rounds = [];
        $matchesInRound = (int) ($bracketSize / 2);

        for ($round = 1; $round <= $roundCount; $round++) {
            $fixtures = [];
            for ($match = 0; $match < $matchesInRound; $match++) {
                $home = $round === 1 ? ($seeded[$match * 2] ?? null) : null;
                $away = $round === 1 ? ($seeded[$match * 2 + 1] ?? null) : null;

                $fixtures[] = [
                    'home_club_id' => $home,
                    'away_club_id' => $away,
                ];
            }

            $rounds[] = [
                'name' => $this->knockoutRoundLabel($matchesInRound, $round),
                'stage' => 'knockout',
                'bracket' => 'main',
                'affects_standings' => false,
                'matches' => $fixtures,
            ];

            $matchesInRound = (int) max(1, floor($matchesInRound / 2));
        }

        return $rounds;
    }

    private function buildDoubleEliminationRounds(array $clubIds): array
    {
        $single = $this->buildSingleEliminationRounds($clubIds);

        if ($single === []) {
            return [];
        }

        $rounds = [];

        foreach ($single as $index => $round) {
            $rounds[] = [
                ...$round,
                'name' => 'Winner Bracket - '.$round['name'],
                'bracket' => 'winner',
            ];

            if ($index < count($single) - 1) {
                $loserMatchCount = max(1, (int) floor(count($round['matches']) / 2));
                $loserMatches = [];
                for ($i = 0; $i < $loserMatchCount; $i++) {
                    $loserMatches[] = ['home_club_id' => null, 'away_club_id' => null];
                }

                $rounds[] = [
                    'name' => 'Loser Bracket R'.($index + 1),
                    'stage' => 'knockout',
                    'bracket' => 'loser',
                    'affects_standings' => false,
                    'matches' => $loserMatches,
                ];
            }
        }

        $rounds[] = [
            'name' => 'Grand Final',
            'stage' => 'final',
            'bracket' => 'grand_final',
            'affects_standings' => false,
            'matches' => [
                ['home_club_id' => null, 'away_club_id' => null],
            ],
        ];

        return $rounds;
    }

    private function buildRoundRobinRounds(array $clubIds, bool $double, string $prefix, bool $isThreeXThree = false): array
    {
        $pairings = $this->roundRobinPairings($clubIds);
        $rounds = [];

        foreach ($pairings as $index => $matches) {
            $rounds[] = [
                'name' => "{$prefix} Matchday ".($index + 1),
                'stage' => 'league',
                'bracket' => 'league',
                'affects_standings' => true,
                'match_type' => $isThreeXThree ? '3x3' : '5x5',
                'matches' => array_map(function (array $pair) use ($isThreeXThree): array {
                    return [
                        'home_club_id' => $pair['home_club_id'],
                        'away_club_id' => $pair['away_club_id'],
                        'ruleset' => $isThreeXThree ? 'fiba_3x3_10min_21pts' : null,
                    ];
                }, $matches),
            ];
        }

        if ($double) {
            $baseCount = count($rounds);
            foreach ($pairings as $index => $matches) {
                $rounds[] = [
                    'name' => "{$prefix} Matchday ".($baseCount + $index + 1),
                    'stage' => 'league',
                    'bracket' => 'league',
                    'affects_standings' => true,
                    'match_type' => $isThreeXThree ? '3x3' : '5x5',
                    'matches' => array_map(function (array $pair) use ($isThreeXThree): array {
                        return [
                            'home_club_id' => $pair['away_club_id'],
                            'away_club_id' => $pair['home_club_id'],
                            'ruleset' => $isThreeXThree ? 'fiba_3x3_10min_21pts' : null,
                        ];
                    }, $matches),
                ];
            }
        }

        return $rounds;
    }

    private function buildGroupKnockoutRounds(array $groupDrawResults, array $settings): array
    {
        if ($groupDrawResults === []) {
            return [];
        }

        $rounds = [];
        foreach ($groupDrawResults as $groupName => $members) {
            $groupPairings = $this->roundRobinPairings($members);
            foreach ($groupPairings as $index => $matches) {
                $rounds[] = [
                    'name' => "{$groupName} Matchday ".($index + 1),
                    'stage' => 'group',
                    'bracket' => 'group',
                    'group' => $groupName,
                    'affects_standings' => true,
                    'matches' => array_map(function (array $pair) use ($groupName): array {
                        return [
                            'home_club_id' => $pair['home_club_id'],
                            'away_club_id' => $pair['away_club_id'],
                            'group' => $groupName,
                        ];
                    }, $matches),
                ];
            }
        }

        $qualifiers = max(2, (int) ($settings['qualifiers_per_group'] ?? 2)) * count($groupDrawResults);
        if ($qualifiers >= 2) {
            $knockout = $this->buildSingleEliminationRounds(array_fill(0, $qualifiers, null));
            foreach ($knockout as $round) {
                $rounds[] = [
                    ...$round,
                    'name' => 'Knockout - '.$round['name'],
                    'stage' => 'knockout',
                    'affects_standings' => false,
                ];
            }
        }

        return $rounds;
    }

    private function resolveGroupDrawResults(
        array $clubIds,
        int $groupCount,
        array $currentDrawResults,
        bool $forceRedraw
    ): array {
        if ($clubIds === [] || $groupCount <= 0) {
            return [];
        }

        $normalizedCurrent = $this->normalizeGroupDrawResults($currentDrawResults);

        if (! $forceRedraw && $this->canReuseGroupDraw($normalizedCurrent, $clubIds, $groupCount)) {
            $ordered = [];
            foreach ($this->buildGroupNames($groupCount) as $groupName) {
                $ordered[$groupName] = $normalizedCurrent[$groupName] ?? [];
            }

            return $ordered;
        }

        $groupNames = $this->buildGroupNames($groupCount);
        $grouped = [];
        foreach ($groupNames as $name) {
            $grouped[$name] = [];
        }

        $shuffledClubIds = array_values($clubIds);
        shuffle($shuffledClubIds);
        $capacities = $this->calculateGroupCapacities(count($shuffledClubIds), count($groupNames));

        $offset = 0;
        foreach ($groupNames as $groupIndex => $groupName) {
            $take = $capacities[$groupIndex] ?? 0;
            $grouped[$groupName] = array_slice($shuffledClubIds, $offset, $take);
            $offset += $take;
        }

        return $grouped;
    }

    private function normalizeGroupDrawResults(array $drawResults): array
    {
        $normalized = [];

        foreach ($drawResults as $groupKey => $members) {
            $groupName = trim((string) $groupKey);

            if ($groupName === '' || ! is_array($members)) {
                continue;
            }

            $normalized[$groupName] = array_values(
                array_filter(array_map(fn ($clubId): string => (string) $clubId, $members), fn (string $clubId): bool => $clubId !== '')
            );
        }

        return $normalized;
    }

    private function canReuseGroupDraw(array $groupDrawResults, array $clubIds, int $groupCount): bool
    {
        if (count($groupDrawResults) !== $groupCount) {
            return false;
        }

        $expectedGroups = $this->buildGroupNames($groupCount);
        foreach ($expectedGroups as $groupName) {
            if (! array_key_exists($groupName, $groupDrawResults)) {
                return false;
            }
        }

        $orderedGroups = [];
        foreach ($expectedGroups as $groupName) {
            $orderedGroups[$groupName] = $groupDrawResults[$groupName];
        }
        $expectedCapacities = $this->calculateGroupCapacities(count($clubIds), $groupCount);
        foreach (array_values($orderedGroups) as $groupIndex => $members) {
            if (count($members) !== ($expectedCapacities[$groupIndex] ?? 0)) {
                return false;
            }
        }

        $drawnClubIds = collect($orderedGroups)
            ->flatten()
            ->filter()
            ->map(fn ($clubId): string => (string) $clubId)
            ->values()
            ->all();

        sort($drawnClubIds);
        $expectedClubIds = array_values(array_map(fn ($clubId): string => (string) $clubId, $clubIds));
        sort($expectedClubIds);

        if ($drawnClubIds !== $expectedClubIds) {
            return false;
        }
        return true;
    }

    private function buildGroupNames(int $groupCount): array
    {
        $names = [];

        for ($i = 0; $i < $groupCount; $i++) {
            $names[] = 'Group '.chr(65 + $i);
        }

        return $names;
    }

    private function calculateGroupCapacities(int $clubCount, int $groupCount): array
    {
        if ($groupCount <= 0) {
            return [];
        }

        $base = intdiv($clubCount, $groupCount);
        $remainder = $clubCount % $groupCount;
        $capacities = [];

        for ($i = 0; $i < $groupCount; $i++) {
            $capacities[] = $base + ($i < $remainder ? 1 : 0);
        }

        return $capacities;
    }

    private function buildSwissRounds(array $clubIds, array $settings): array
    {
        if (count($clubIds) < 2) {
            return [];
        }

        $roundCount = max(1, (int) ($settings['rounds'] ?? 3));
        $rounds = [];

        for ($round = 1; $round <= $roundCount; $round++) {
            $pairs = [];

            if ($round === 1) {
                $pairs = $this->pairSequential($clubIds);
            } else {
                $pairCount = (int) ceil(count($clubIds) / 2);
                for ($i = 0; $i < $pairCount; $i++) {
                    $pairs[] = ['home_club_id' => null, 'away_club_id' => null];
                }
            }

            $rounds[] = [
                'name' => 'Swiss Round '.$round,
                'stage' => 'swiss',
                'bracket' => 'swiss',
                'affects_standings' => true,
                'matches' => $pairs,
            ];
        }

        return $rounds;
    }

    private function buildBestOfSeriesRounds(array $clubIds, array $settings): array
    {
        if (count($clubIds) < 2) {
            return [];
        }

        $bestOf = $this->normalizeBestOf((int) ($settings['best_of'] ?? 3));
        $pairs = $this->pairSequential($clubIds);
        $rounds = [];

        foreach ($pairs as $seriesIndex => $pair) {
            $matches = [];
            for ($game = 1; $game <= $bestOf; $game++) {
                $matches[] = [
                    'home_club_id' => $pair['home_club_id'],
                    'away_club_id' => $pair['away_club_id'],
                    'best_of' => $bestOf,
                    'series_key' => 'SERIES-'.($seriesIndex + 1),
                ];
            }

            $rounds[] = [
                'name' => 'Series '.($seriesIndex + 1)." (Best of {$bestOf})",
                'stage' => 'series',
                'bracket' => 'series',
                'affects_standings' => false,
                'matches' => $matches,
            ];
        }

        return $rounds;
    }

    private function buildPlayInRounds(array $clubIds, array $settings): array
    {
        if (count($clubIds) < 4) {
            return $this->buildSingleEliminationRounds($clubIds);
        }

        $slotCount = min(max(2, (int) ($settings['play_in_slots'] ?? 4)), count($clubIds));
        if ($slotCount % 2 !== 0) {
            $slotCount--;
        }
        $slotCount = max(2, $slotCount);

        $playInTeams = array_slice($clubIds, -$slotCount);
        $topSeeds = array_slice($clubIds, 0, count($clubIds) - $slotCount);
        $playInPairs = $this->pairSequential($playInTeams);

        $rounds = [
            [
                'name' => 'Play-In Round',
                'stage' => 'play_in',
                'bracket' => 'play_in',
                'affects_standings' => false,
                'matches' => $playInPairs,
            ],
        ];

        $mainBracketParticipants = array_merge($topSeeds, array_fill(0, count($playInPairs), null));
        $knockout = $this->buildSingleEliminationRounds($mainBracketParticipants);
        foreach ($knockout as $round) {
            $rounds[] = [
                ...$round,
                'name' => 'Main Bracket - '.$round['name'],
            ];
        }

        return $rounds;
    }

    private function buildConsolationRounds(array $clubIds): array
    {
        $rounds = $this->buildSingleEliminationRounds($clubIds);

        if (count($clubIds) >= 4) {
            $rounds[] = [
                'name' => 'Perebutan Juara 3',
                'stage' => 'consolation',
                'bracket' => 'placement',
                'affects_standings' => false,
                'matches' => [
                    ['home_club_id' => null, 'away_club_id' => null],
                ],
            ];
        }

        if (count($clubIds) >= 8) {
            $rounds[] = [
                'name' => 'Perebutan Peringkat 5-8',
                'stage' => 'consolation',
                'bracket' => 'placement',
                'affects_standings' => false,
                'matches' => [
                    ['home_club_id' => null, 'away_club_id' => null],
                    ['home_club_id' => null, 'away_club_id' => null],
                ],
            ];
        }

        return $rounds;
    }

    private function buildJamboreeRounds(array $clubIds): array
    {
        $pairs = $this->roundRobinPairings($clubIds);
        $rounds = [];

        foreach ($pairs as $index => $matches) {
            $rounds[] = [
                'name' => 'Showcase Session '.($index + 1),
                'stage' => 'showcase',
                'bracket' => 'showcase',
                'affects_standings' => false,
                'matches' => $matches,
            ];
        }

        return $rounds;
    }

    private function buildLadderRounds(array $clubIds, array $settings): array
    {
        if (count($clubIds) < 2) {
            return [];
        }

        $roundCount = max(1, (int) ($settings['rounds'] ?? 3));
        $rounds = [];

        for ($round = 1; $round <= $roundCount; $round++) {
            $pairs = [];
            $offset = ($round - 1) % 2;
            for ($i = $offset; $i < count($clubIds) - 1; $i += 2) {
                $pairs[] = [
                    'home_club_id' => $clubIds[$i + 1] ?? null,
                    'away_club_id' => $clubIds[$i] ?? null,
                ];
            }

            if ($pairs === []) {
                $pairs = $this->pairSequential($clubIds);
            }

            $rounds[] = [
                'name' => 'Challenge Week '.$round,
                'stage' => 'ladder',
                'bracket' => 'ladder',
                'affects_standings' => true,
                'matches' => $pairs,
            ];
        }

        return $rounds;
    }

    private function buildDraftLeagueRounds(array $clubIds): array
    {
        if (count($clubIds) < 2) {
            return [];
        }

        $pairs = $this->roundRobinPairings($clubIds);
        $rounds = [];

        foreach ($pairs as $index => $matches) {
            $rounds[] = [
                'name' => 'Draft League Matchday '.($index + 1),
                'stage' => 'draft_league',
                'bracket' => 'draft',
                'affects_standings' => true,
                'matches' => $matches,
            ];
        }

        return $rounds;
    }

    private function roundRobinPairings(array $clubIds): array
    {
        $teams = array_values($clubIds);
        if (count($teams) < 2) {
            return [];
        }

        if (count($teams) % 2 !== 0) {
            $teams[] = null;
        }

        $rounds = [];
        $teamCount = count($teams);
        $half = (int) ($teamCount / 2);
        $rotation = $teams;

        for ($round = 0; $round < $teamCount - 1; $round++) {
            $pairs = [];

            for ($i = 0; $i < $half; $i++) {
                $home = $rotation[$i];
                $away = $rotation[$teamCount - 1 - $i];

                if ($home === null || $away === null) {
                    continue;
                }

                $pairs[] = $round % 2 === 0
                    ? ['home_club_id' => $home, 'away_club_id' => $away]
                    : ['home_club_id' => $away, 'away_club_id' => $home];
            }

            if ($pairs !== []) {
                $rounds[] = $pairs;
            }

            $pivot = $rotation[0];
            $others = array_slice($rotation, 1);
            $last = array_pop($others);
            array_unshift($others, $last);
            $rotation = array_merge([$pivot], $others);
        }

        return $rounds;
    }

    private function pairSequential(array $clubIds): array
    {
        $pairs = [];
        $ids = array_values($clubIds);

        if (count($ids) % 2 !== 0) {
            $ids[] = null;
        }

        for ($i = 0; $i < count($ids); $i += 2) {
            $pairs[] = [
                'home_club_id' => $ids[$i] ?? null,
                'away_club_id' => $ids[$i + 1] ?? null,
            ];
        }

        return $pairs;
    }

    private function seedClubIds(array $clubs): array
    {
        return collect($clubs)
            ->sortBy(function (array $club): string {
                return Str::lower((string) ($club['name'] ?? ''));
            })
            ->pluck('id')
            ->filter()
            ->values()
            ->all();
    }

    private function clampInt(int $value, int $min, int $max): int
    {
        return max($min, min($max, $value));
    }

    private function normalizeBestOf(int $value): int
    {
        return in_array($value, [3, 5, 7], true) ? $value : 3;
    }

    private function knockoutRoundLabel(int $matchesInRound, int $round): string
    {
        return match ($matchesInRound) {
            1 => 'Final',
            2 => 'Semifinal',
            4 => 'Perempat Final',
            8 => '16 Besar',
            default => 'Babak '.$round,
        };
    }
}
