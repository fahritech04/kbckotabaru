<?php

namespace App\Services;

use RuntimeException;

class TournamentAutomationService
{
    public function __construct(
        private readonly KbcRepository $repository,
        private readonly TournamentSystemService $systemService
    ) {}

    public function syncTournament(string $tournamentId, array $options = []): array
    {
        $tournament = $this->repository->findTournament($tournamentId);

        if ($tournament === null) {
            throw new RuntimeException('Turnamen tidak ditemukan.');
        }

        $forceGroupRedraw = (bool) ($options['force_group_redraw'] ?? false);
        if (array_key_exists('group_draw_results', $options) && is_array($options['group_draw_results'])) {
            $tournament['group_draw_results'] = $options['group_draw_results'];
        }

        $clubs = $this->repository->listClubsByTournament($tournamentId);
        $plan = $this->systemService->generatePlan($tournament, $clubs, [
            'force_group_redraw' => $forceGroupRedraw,
        ]);

        $existingMatches = $this->repository->listMatchesByTournament($tournamentId);
        $existingSchedules = $this->repository->listSchedulesByTournament($tournamentId);

        $deletedMatches = 0;
        $deletedSchedules = 0;

        foreach ($existingMatches as $match) {
            if (($match['generated_by_system'] ?? false) !== true) {
                continue;
            }

            if ($this->repository->deleteMatch($match['id'])) {
                $deletedMatches++;
            }
        }

        foreach ($existingSchedules as $schedule) {
            if (($schedule['generated_by_system'] ?? false) !== true) {
                continue;
            }

            if ($this->repository->deleteSchedule($schedule['id'])) {
                $deletedSchedules++;
            }
        }

        $scheduleMap = [];
        $createdSchedules = 0;
        $createdMatches = 0;

        foreach ($plan['schedules'] as $schedulePayload) {
            $created = $this->repository->createSchedule([
                'tournament_id' => $tournamentId,
                'title' => $schedulePayload['title'],
                'venue' => $schedulePayload['venue'],
                'scheduled_at' => $schedulePayload['scheduled_at'],
                'status' => $schedulePayload['status'],
                'notes' => $schedulePayload['notes'] ?? null,
                'generated_by_system' => true,
                'competition_system' => $plan['system_code'],
                'stage' => $schedulePayload['stage'] ?? null,
            ]);

            $scheduleMap[$schedulePayload['reference']] = $created['id'];
            $createdSchedules++;
        }

        foreach ($plan['matches'] as $matchPayload) {
            $this->repository->createMatch([
                'tournament_id' => $tournamentId,
                'schedule_id' => $scheduleMap[$matchPayload['schedule_reference']] ?? null,
                'home_club_id' => $matchPayload['home_club_id'] ?? null,
                'away_club_id' => $matchPayload['away_club_id'] ?? null,
                'round' => $matchPayload['round'],
                'tipoff_at' => $matchPayload['tipoff_at'],
                'venue' => $matchPayload['venue'],
                'status' => $matchPayload['status'] ?? 'scheduled',
                'home_score' => (int) ($matchPayload['home_score'] ?? 0),
                'away_score' => (int) ($matchPayload['away_score'] ?? 0),
                'highlight' => $matchPayload['highlight'] ?? null,
                'generated_by_system' => true,
                'competition_system' => $plan['system_code'],
                'stage' => $matchPayload['stage'] ?? null,
                'bracket' => $matchPayload['bracket'] ?? null,
                'group' => $matchPayload['group'] ?? null,
                'affects_standings' => (bool) ($matchPayload['affects_standings'] ?? false),
                'best_of' => $matchPayload['best_of'] ?? null,
                'series_key' => $matchPayload['series_key'] ?? null,
                'match_type' => $matchPayload['match_type'] ?? '5x5',
                'ruleset' => $matchPayload['ruleset'] ?? null,
            ]);
            $createdMatches++;
        }

        $this->repository->updateTournament($tournamentId, [
            'competition_system' => $plan['system_code'],
            'competition_system_label' => $plan['system_label'],
            'competition_settings' => $plan['settings'],
            'group_draw_results' => $plan['group_draw_results'] ?? [],
            'competition_rounds' => collect($plan['rounds'])->map(function (array $round): array {
                return [
                    'name' => $round['name'] ?? '-',
                    'stage' => $round['stage'] ?? 'main',
                    'bracket' => $round['bracket'] ?? 'main',
                    'group' => $round['group'] ?? null,
                    'matches_count' => count($round['matches'] ?? []),
                    'affects_standings' => (bool) ($round['affects_standings'] ?? false),
                ];
            })->values()->all(),
            'standings_enabled' => (bool) ($plan['supports_standings'] ?? false),
            'participants_count' => count($clubs),
            'system_last_sync_at' => now()->toIso8601String(),
        ]);

        return [
            'system_code' => $plan['system_code'],
            'system_label' => $plan['system_label'],
            'clubs_count' => count($clubs),
            'deleted_matches' => $deletedMatches,
            'deleted_schedules' => $deletedSchedules,
            'created_matches' => $createdMatches,
            'created_schedules' => $createdSchedules,
            'group_draw_results' => $plan['group_draw_results'] ?? [],
        ];
    }
}
