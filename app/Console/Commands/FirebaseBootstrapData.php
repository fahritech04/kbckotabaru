<?php

namespace App\Console\Commands;

use App\Services\KbcRepository;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

#[Signature('app:firebase-bootstrap-data
    {--admin-email=admin@kbckotabaru.id : Email akun admin}
    {--admin-password=admin12345 : Password akun admin}
    {--force : Paksa buat sample data walau koleksi sudah berisi data}')]
#[Description('Bootstrap akun admin dan sample data KBC ke Firebase Realtime Database')]
class FirebaseBootstrapData extends Command
{
    public function __construct(private readonly KbcRepository $repository)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (! $this->repository->isFirebaseReady()) {
            $this->error('Firebase belum siap. Pastikan FIREBASE_CREDENTIALS/GOOGLE_APPLICATION_CREDENTIALS benar.');
            $this->line('Detail: '.($this->repository->firebaseError() ?? '-'));

            return self::FAILURE;
        }

        $adminEmail = (string) $this->option('admin-email');
        $adminPassword = (string) $this->option('admin-password');
        $force = (bool) $this->option('force');

        $adminUser = $this->repository->findUserByEmail($adminEmail);

        if ($adminUser === null) {
            $this->repository->createUser([
                'name' => 'Super Admin KBC',
                'email' => $adminEmail,
                'password' => Hash::make($adminPassword),
                'role' => 'admin',
            ]);

            $this->info("Admin berhasil dibuat: {$adminEmail}");
        } else {
            $this->warn("Admin sudah ada: {$adminEmail}");
        }

        if ($force || count($this->repository->listTournaments()) === 0) {
            $this->seedSampleLeagueData();
            $this->info('Sample data berhasil dibuat.');
        } else {
            $this->warn('Sample data tidak dibuat karena koleksi sudah berisi data. Gunakan --force untuk memaksa.');
        }

        $this->line('Selesai. Login admin dengan akun di atas melalui halaman /admin/login.');

        return self::SUCCESS;
    }

    private function seedSampleLeagueData(): void
    {
        $tournament = $this->repository->createTournament([
            'name' => 'KBC Championship',
            'season' => now()->format('Y'),
            'location' => 'GOR Kotabaru Arena',
            'start_date' => now()->startOfMonth()->toDateString(),
            'end_date' => now()->addMonths(2)->endOfMonth()->toDateString(),
            'status' => 'ongoing',
            'description' => 'Kompetisi basket antarklub terbaik se-Kotabaru.',
            'hero_image' => 'https://images.unsplash.com/photo-1546519638-68e109498ffc?auto=format&fit=crop&w=1600&q=80',
            'slug' => 'kbc-championship-'.now()->format('Y'),
        ]);

        $clubs = [
            ['name' => 'Kotabaru Tigers', 'city' => 'Kotabaru', 'coach' => 'Rangga Pratama', 'wins' => 8, 'losses' => 2],
            ['name' => 'Sea Hawks', 'city' => 'Kotabaru', 'coach' => 'Dani Saputra', 'wins' => 6, 'losses' => 4],
            ['name' => 'Borneo Flames', 'city' => 'Kotabaru', 'coach' => 'Arif Nugroho', 'wins' => 7, 'losses' => 3],
            ['name' => 'Black Mambas', 'city' => 'Kotabaru', 'coach' => 'Rizky Hidayat', 'wins' => 5, 'losses' => 5],
        ];

        $clubData = [];
        foreach ($clubs as $club) {
            $clubRow = $this->repository->createClub([
                ...$club,
                'founded_year' => random_int(2005, 2018),
                'logo_url' => null,
                'tournament_id' => $tournament['id'],
                'manager_name' => 'Manajer '.$club['name'],
                'manager_email' => strtolower(str_replace(' ', '', $club['name'])).'@example.com',
                'manager_phone' => '0812'.random_int(10000000, 99999999),
                'description' => "{$club['name']} adalah salah satu kontestan utama di KBC Championship.",
            ]);

            $clubData[] = $clubRow;

            for ($i = 1; $i <= 5; $i++) {
                $this->repository->createPlayer([
                    'club_id' => $clubRow['id'],
                    'name' => "{$club['name']} Player {$i}",
                    'jersey_number' => random_int(1, 99),
                    'position' => ['Guard', 'Forward', 'Center'][array_rand(['Guard', 'Forward', 'Center'])],
                    'photo_url' => null,
                ]);
            }
        }

        $scheduleRound1 = $this->repository->createSchedule([
            'tournament_id' => $tournament['id'],
            'title' => 'Round 1 Matchday',
            'venue' => 'GOR Kotabaru Arena',
            'scheduled_at' => now()->addDays(2)->setTime(15, 0)->toIso8601String(),
            'status' => 'published',
            'notes' => 'Pertandingan pembuka musim.',
        ]);

        $scheduleRound2 = $this->repository->createSchedule([
            'tournament_id' => $tournament['id'],
            'title' => 'Round 2 Matchday',
            'venue' => 'GOR Kotabaru Arena',
            'scheduled_at' => now()->addDays(5)->setTime(18, 0)->toIso8601String(),
            'status' => 'published',
            'notes' => 'Pertandingan lanjutan fase regular.',
        ]);

        $this->repository->createMatch([
            'tournament_id' => $tournament['id'],
            'schedule_id' => $scheduleRound1['id'],
            'home_club_id' => $clubData[0]['id'],
            'away_club_id' => $clubData[1]['id'],
            'round' => 'Round 1',
            'tipoff_at' => now()->addDays(2)->setTime(15, 0)->toIso8601String(),
            'venue' => 'GOR Kotabaru Arena',
            'status' => 'scheduled',
            'home_score' => 0,
            'away_score' => 0,
            'highlight' => 'Game pembuka KBC Championship.',
        ]);

        $this->repository->createMatch([
            'tournament_id' => $tournament['id'],
            'schedule_id' => $scheduleRound1['id'],
            'home_club_id' => $clubData[2]['id'],
            'away_club_id' => $clubData[3]['id'],
            'round' => 'Round 1',
            'tipoff_at' => now()->addDays(2)->setTime(19, 0)->toIso8601String(),
            'venue' => 'GOR Kotabaru Arena',
            'status' => 'scheduled',
            'home_score' => 0,
            'away_score' => 0,
            'highlight' => 'Duel sengit perebutan poin awal.',
        ]);

        $this->repository->createMatch([
            'tournament_id' => $tournament['id'],
            'schedule_id' => $scheduleRound2['id'],
            'home_club_id' => $clubData[0]['id'],
            'away_club_id' => $clubData[2]['id'],
            'round' => 'Round 2',
            'tipoff_at' => now()->addDays(5)->setTime(18, 30)->toIso8601String(),
            'venue' => 'GOR Kotabaru Arena',
            'status' => 'scheduled',
            'home_score' => 0,
            'away_score' => 0,
            'highlight' => 'Ujian besar dua kandidat juara.',
        ]);
    }
}
