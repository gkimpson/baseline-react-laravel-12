# Backend Structure Document
## Pro Clubs - Laravel Architecture & Implementation

---

### **Document Information**
**Application:** Pro Clubs  
**Version:** 1.0  
**Date:** June 14, 2025  
**Document Type:** Backend Architecture Documentation  

---

## **1. Overview**

This document outlines the complete backend architecture for Pro Clubs, built on Laravel 12. The backend serves as the core system for managing FIFA/EA Sports FC Pro Clubs data, providing robust APIs, data processing, and integration capabilities.

---

## **2. Application Architecture**

### **2.1 Laravel Application Structure**
```
app/
├── Console/                 # Artisan commands
├── Events/                  # Event classes
├── Exceptions/              # Custom exception handlers
├── Http/                    # HTTP layer
│   ├── Controllers/         # Route controllers
│   ├── Middleware/          # HTTP middleware
│   ├── Requests/            # Form request validation
│   └── Resources/           # API resource transformations
├── Jobs/                    # Queue job classes
├── Listeners/               # Event listeners
├── Mail/                    # Email templates
├── Models/                  # Eloquent models
├── Notifications/           # Notification classes
├── Policies/                # Authorization policies
├── Providers/               # Service providers
├── Rules/                   # Custom validation rules
└── Services/                # Business logic services
```

### **2.2 Domain-Driven Design Structure**
```
app/Domains/
├── Auth/                    # Authentication domain
│   ├── Models/
│   ├── Services/
│   ├── Policies/
│   └── Events/
├── Players/                 # Player management domain
│   ├── Models/
│   ├── Services/
│   ├── Repositories/
│   ├── Policies/
│   └── Events/
├── Clubs/                   # Club management domain
├── Matches/                 # Match results domain
├── Community/               # Community features domain
└── Integration/             # EA Sports API integration domain
```

---

## **3. Database Architecture**

### **3.1 Database Schema Design**

#### **Core Entities**
```sql
-- Users table
CREATE TABLE users (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    avatar_url VARCHAR(500) NULL,
    preferred_platform ENUM('pc', 'playstation', 'xbox', 'switch') DEFAULT 'pc',
    is_active BOOLEAN DEFAULT TRUE,
    last_login_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_last_login (last_login_at)
);

-- Players table
CREATE TABLE players (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NULL,
    ea_player_id VARCHAR(100) NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    position ENUM('GK','CB','LB','RB','CDM','CM','CAM','LM','RM','LW','RW','ST') NOT NULL,
    overall_rating TINYINT UNSIGNED DEFAULT 0,
    preferred_foot ENUM('left', 'right', 'both') DEFAULT 'right',
    height SMALLINT UNSIGNED NULL,
    weight SMALLINT UNSIGNED NULL,
    nationality VARCHAR(3) NULL,
    platform ENUM('pc', 'playstation', 'xbox', 'switch') NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    is_cheater BOOLEAN DEFAULT FALSE,
    performance_trend ENUM('rising', 'stable', 'decline', 'world_class') DEFAULT 'stable',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_ea_player_id (ea_player_id),
    INDEX idx_position (position),
    INDEX idx_platform (platform),
    INDEX idx_overall_rating (overall_rating),
    INDEX idx_name (name),
    FULLTEXT idx_search (name)
);

-- Clubs table
CREATE TABLE clubs (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    ea_club_id VARCHAR(100) NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    short_name VARCHAR(50) NULL,
    badge_url VARCHAR(500) NULL,
    platform ENUM('pc', 'playstation', 'xbox', 'switch') NOT NULL,
    division TINYINT UNSIGNED DEFAULT 10,
    points MEDIUMINT UNSIGNED DEFAULT 0,
    matches_played MEDIUMINT UNSIGNED DEFAULT 0,
    wins MEDIUMINT UNSIGNED DEFAULT 0,
    draws MEDIUMINT UNSIGNED DEFAULT 0,
    losses MEDIUMINT UNSIGNED DEFAULT 0,
    goals_for MEDIUMINT UNSIGNED DEFAULT 0,
    goals_against MEDIUMINT UNSIGNED DEFAULT 0,
    clean_sheets MEDIUMINT UNSIGNED DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    last_match_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_ea_club_id (ea_club_id),
    INDEX idx_platform (platform),
    INDEX idx_division (division),
    INDEX idx_points (points),
    INDEX idx_name (name),
    FULLTEXT idx_search (name, short_name)
);
```

#### **Player Attributes System**
```sql
-- Player attributes table
CREATE TABLE player_attributes (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    player_id BIGINT UNSIGNED NOT NULL,
    -- Outfield player attributes
    pace TINYINT UNSIGNED DEFAULT 0,
    shooting TINYINT UNSIGNED DEFAULT 0,
    passing TINYINT UNSIGNED DEFAULT 0,
    dribbling TINYINT UNSIGNED DEFAULT 0,
    defending TINYINT UNSIGNED DEFAULT 0,
    physicality TINYINT UNSIGNED DEFAULT 0,
    -- Detailed attributes
    acceleration TINYINT UNSIGNED DEFAULT 0,
    sprint_speed TINYINT UNSIGNED DEFAULT 0,
    positioning TINYINT UNSIGNED DEFAULT 0,
    finishing TINYINT UNSIGNED DEFAULT 0,
    shot_power TINYINT UNSIGNED DEFAULT 0,
    long_shots TINYINT UNSIGNED DEFAULT 0,
    volleys TINYINT UNSIGNED DEFAULT 0,
    penalties TINYINT UNSIGNED DEFAULT 0,
    vision TINYINT UNSIGNED DEFAULT 0,
    crossing TINYINT UNSIGNED DEFAULT 0,
    free_kick_accuracy TINYINT UNSIGNED DEFAULT 0,
    short_passing TINYINT UNSIGNED DEFAULT 0,
    long_passing TINYINT UNSIGNED DEFAULT 0,
    curve TINYINT UNSIGNED DEFAULT 0,
    agility TINYINT UNSIGNED DEFAULT 0,
    balance TINYINT UNSIGNED DEFAULT 0,
    reactions TINYINT UNSIGNED DEFAULT 0,
    ball_control TINYINT UNSIGNED DEFAULT 0,
    dribbling_skill TINYINT UNSIGNED DEFAULT 0,
    composure TINYINT UNSIGNED DEFAULT 0,
    interceptions TINYINT UNSIGNED DEFAULT 0,
    heading_accuracy TINYINT UNSIGNED DEFAULT 0,
    def_awareness TINYINT UNSIGNED DEFAULT 0,
    standing_tackle TINYINT UNSIGNED DEFAULT 0,
    sliding_tackle TINYINT UNSIGNED DEFAULT 0,
    jumping TINYINT UNSIGNED DEFAULT 0,
    stamina TINYINT UNSIGNED DEFAULT 0,
    strength TINYINT UNSIGNED DEFAULT 0,
    aggression TINYINT UNSIGNED DEFAULT 0,
    -- Goalkeeper attributes
    gk_diving TINYINT UNSIGNED DEFAULT 0,
    gk_handling TINYINT UNSIGNED DEFAULT 0,
    gk_kicking TINYINT UNSIGNED DEFAULT 0,
    gk_positioning TINYINT UNSIGNED DEFAULT 0,
    gk_reflexes TINYINT UNSIGNED DEFAULT 0,
    gk_speed TINYINT UNSIGNED DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE CASCADE,
    UNIQUE KEY unique_player_attributes (player_id)
);
```

#### **Match Results System**
```sql
-- Results table
CREATE TABLE results (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    ea_match_id VARCHAR(100) NULL UNIQUE,
    home_club_id BIGINT UNSIGNED NOT NULL,
    away_club_id BIGINT UNSIGNED NOT NULL,
    home_score TINYINT UNSIGNED NOT NULL,
    away_score TINYINT UNSIGNED NOT NULL,
    match_type ENUM('league', 'cup', 'friendly', 'tournament') DEFAULT 'league',
    platform ENUM('pc', 'playstation', 'xbox', 'switch') NOT NULL,
    played_at TIMESTAMP NOT NULL,
    duration_minutes TINYINT UNSIGNED DEFAULT 90,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (home_club_id) REFERENCES clubs(id) ON DELETE CASCADE,
    FOREIGN KEY (away_club_id) REFERENCES clubs(id) ON DELETE CASCADE,
    INDEX idx_clubs (home_club_id, away_club_id),
    INDEX idx_played_at (played_at),
    INDEX idx_platform (platform),
    INDEX idx_match_type (match_type)
);

-- Player statistics per match
CREATE TABLE result_player_stats (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    result_id BIGINT UNSIGNED NOT NULL,
    player_id BIGINT UNSIGNED NOT NULL,
    club_id BIGINT UNSIGNED NOT NULL,
    position ENUM('GK','CB','LB','RB','CDM','CM','CAM','LM','RM','LW','RW','ST') NOT NULL,
    rating DECIMAL(3,1) DEFAULT 0.0,
    goals TINYINT UNSIGNED DEFAULT 0,
    assists TINYINT UNSIGNED DEFAULT 0,
    shots TINYINT UNSIGNED DEFAULT 0,
    shots_on_target TINYINT UNSIGNED DEFAULT 0,
    passes_completed SMALLINT UNSIGNED DEFAULT 0,
    passes_attempted SMALLINT UNSIGNED DEFAULT 0,
    pass_accuracy DECIMAL(5,2) DEFAULT 0.00,
    tackles_won TINYINT UNSIGNED DEFAULT 0,
    tackles_attempted TINYINT UNSIGNED DEFAULT 0,
    interceptions TINYINT UNSIGNED DEFAULT 0,
    clearances TINYINT UNSIGNED DEFAULT 0,
    saves TINYINT UNSIGNED DEFAULT 0,
    yellow_cards TINYINT UNSIGNED DEFAULT 0,
    red_cards TINYINT UNSIGNED DEFAULT 0,
    minutes_played TINYINT UNSIGNED DEFAULT 0,
    man_of_match BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (result_id) REFERENCES results(id) ON DELETE CASCADE,
    FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE CASCADE,
    FOREIGN KEY (club_id) REFERENCES clubs(id) ON DELETE CASCADE,
    INDEX idx_result_player (result_id, player_id),
    INDEX idx_player_stats (player_id, rating),
    INDEX idx_goals (goals),
    INDEX idx_assists (assists)
);
```

### **3.2 Database Relationships**
```php
// Player Model Relationships
class Player extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attributes(): HasOne
    {
        return $this->hasOne(PlayerAttribute::class);
    }

    public function currentClub(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'current_club_id');
    }

    public function transfers(): HasMany
    {
        return $this->hasMany(PlayerTransfer::class);
    }

    public function matchStats(): HasMany
    {
        return $this->hasMany(ResultPlayerStat::class);
    }

    public function achievements(): BelongsToMany
    {
        return $this->belongsToMany(Achievement::class)
                    ->withPivot('earned_at', 'match_id')
                    ->withTimestamps();
    }
}

// Club Model Relationships
class Club extends Model
{
    public function players(): HasMany
    {
        return $this->hasMany(Player::class, 'current_club_id');
    }

    public function homeMatches(): HasMany
    {
        return $this->hasMany(Result::class, 'home_club_id');
    }

    public function awayMatches(): HasMany
    {
        return $this->hasMany(Result::class, 'away_club_id');
    }

    public function allMatches()
    {
        return $this->homeMatches()->union($this->awayMatches());
    }

    public function transfers(): HasMany
    {
        return $this->hasMany(PlayerTransfer::class, 'to_club_id');
    }
}
```

---

## **4. API Architecture**

### **4.1 RESTful API Design**
```php
// API Routes Structure
Route::prefix('api/v1')->group(function () {
    // Authentication routes
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    
    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        // Player management
        Route::apiResource('players', PlayerController::class);
        Route::get('/players/{player}/stats', [PlayerController::class, 'stats']);
        Route::get('/players/{player}/matches', [PlayerController::class, 'matches']);
        Route::post('/players/{player}/claim', [PlayerController::class, 'claim']);
        
        // Club management
        Route::apiResource('clubs', ClubController::class);
        Route::get('/clubs/{club}/members', [ClubController::class, 'members']);
        Route::get('/clubs/{club}/stats', [ClubController::class, 'stats']);
        Route::post('/clubs/{club}/join', [ClubController::class, 'join']);
        Route::delete('/clubs/{club}/leave', [ClubController::class, 'leave']);
        
        // Match results
        Route::apiResource('results', ResultController::class)->only(['index', 'show']);
        Route::get('/results/{result}/details', [ResultController::class, 'details']);
        
        // Community features
        Route::get('/leaderboards/players', [LeaderboardController::class, 'players']);
        Route::get('/leaderboards/clubs', [LeaderboardController::class, 'clubs']);
        Route::apiResource('tournaments', TournamentController::class);
        
        // User preferences
        Route::get('/profile', [UserController::class, 'profile']);
        Route::put('/profile', [UserController::class, 'updateProfile']);
        Route::get('/favorites', [UserController::class, 'favorites']);
        Route::post('/favorites/clubs/{club}', [UserController::class, 'favoriteClub']);
    });
    
    // Public routes
    Route::get('/search/players', [SearchController::class, 'players']);
    Route::get('/search/clubs', [SearchController::class, 'clubs']);
    Route::get('/public/leaderboards', [PublicController::class, 'leaderboards']);
});
```

### **4.2 Laravel Orion Integration**
```php
// Player Orion Controller
class PlayerController extends Controller
{
    use HandlesStandardOperations;

    protected $model = Player::class;

    protected function searchableBy(): array
    {
        return ['name', 'position', 'platform'];
    }

    protected function filterableBy(): array
    {
        return [
            'position', 'platform', 'overall_rating', 
            'current_club_id', 'is_active', 'nationality'
        ];
    }

    protected function sortableBy(): array
    {
        return [
            'name', 'overall_rating', 'created_at', 
            'position', 'matches_played'
        ];
    }

    protected function includes(): array
    {
        return ['user', 'attributes', 'currentClub', 'stats'];
    }

    protected function aggregates(): array
    {
        return [
            'avg_rating' => 'match_stats.rating',
            'total_goals' => 'match_stats.goals',
            'total_assists' => 'match_stats.assists'
        ];
    }
}
```

### **4.3 API Resource Transformations**
```php
// Player API Resource
class PlayerResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'position' => $this->position,
            'overall_rating' => $this->overall_rating,
            'platform' => $this->platform,
            'nationality' => $this->nationality,
            'performance_trend' => $this->performance_trend,
            'is_active' => $this->is_active,
            'stats' => [
                'matches_played' => $this->match_stats_count,
                'goals' => $this->match_stats_sum_goals,
                'assists' => $this->match_stats_sum_assists,
                'average_rating' => round($this->match_stats_avg_rating, 2),
            ],
            'attributes' => new PlayerAttributeResource($this->whenLoaded('attributes')),
            'current_club' => new ClubResource($this->whenLoaded('currentClub')),
            'user' => new UserResource($this->whenLoaded('user')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

// Collection Resource with Pagination
class PlayerCollection extends ResourceCollection
{
    public function toArray($request): array
    {
        return [
            'data' => PlayerResource::collection($this->collection),
            'meta' => [
                'total' => $this->total(),
                'per_page' => $this->perPage(),
                'current_page' => $this->currentPage(),
                'last_page' => $this->lastPage(),
                'from' => $this->firstItem(),
                'to' => $this->lastItem(),
            ],
            'links' => [
                'first' => $this->url(1),
                'last' => $this->url($this->lastPage()),
                'prev' => $this->previousPageUrl(),
                'next' => $this->nextPageUrl(),
            ],
        ];
    }
}
```

---

## **5. Service Layer Architecture**

### **5.1 Business Logic Services**
```php
// Player Management Service
class PlayerService
{
    public function __construct(
        private PlayerRepository $playerRepository,
        private EASportsApiService $eaApiService,
        private StatisticsService $statisticsService
    ) {}

    public function createPlayer(CreatePlayerRequest $request): Player
    {
        DB::beginTransaction();
        
        try {
            $player = $this->playerRepository->create($request->validated());
            
            // Sync with EA Sports API if EA Player ID provided
            if ($request->ea_player_id) {
                $this->syncPlayerWithEA($player);
            }
            
            // Initialize player attributes
            $this->initializePlayerAttributes($player);
            
            // Dispatch events
            event(new PlayerCreated($player));
            
            DB::commit();
            return $player->load(['attributes', 'currentClub']);
            
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updatePlayerStats(Player $player, array $matchStats): void
    {
        $this->statisticsService->updatePlayerStatistics($player, $matchStats);
        $this->updatePlayerRating($player);
        $this->checkForAchievements($player, $matchStats);
    }

    public function searchPlayers(PlayerSearchRequest $request): LengthAwarePaginator
    {
        return $this->playerRepository->searchWithFilters(
            $request->search_term,
            $request->filters(),
            $request->per_page ?? 20
        );
    }

    private function syncPlayerWithEA(Player $player): void
    {
        $eaData = $this->eaApiService->getPlayerData($player->ea_player_id);
        
        if ($eaData) {
            $player->update([
                'overall_rating' => $eaData['overall'],
                'height' => $eaData['height'],
                'weight' => $eaData['weight'],
                'preferred_foot' => $eaData['preferred_foot'],
            ]);
            
            $this->updatePlayerAttributes($player, $eaData['attributes']);
        }
    }
}

// Club Management Service
class ClubService
{
    public function __construct(
        private ClubRepository $clubRepository,
        private PlayerService $playerService,
        private MatchService $matchService
    ) {}

    public function updateClubStats(Club $club): Club
    {
        $stats = $this->calculateClubStatistics($club);
        
        $club->update([
            'matches_played' => $stats['matches_played'],
            'wins' => $stats['wins'],
            'draws' => $stats['draws'],
            'losses' => $stats['losses'],
            'goals_for' => $stats['goals_for'],
            'goals_against' => $stats['goals_against'],
            'clean_sheets' => $stats['clean_sheets'],
            'points' => $stats['points'],
        ]);

        return $club;
    }

    public function addPlayerToClub(Club $club, Player $player): void
    {
        DB::beginTransaction();
        
        try {
            // Remove player from current club
            if ($player->current_club_id) {
                $this->removePlayerFromClub($player->currentClub, $player);
            }
            
            // Add to new club
            $player->update(['current_club_id' => $club->id]);
            
            // Record transfer
            PlayerTransfer::create([
                'player_id' => $player->id,
                'from_club_id' => $player->getOriginal('current_club_id'),
                'to_club_id' => $club->id,
                'transferred_at' => now(),
            ]);
            
            // Dispatch events
            event(new PlayerTransferred($player, $club));
            
            DB::commit();
            
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
```

### **5.2 EA Sports API Integration Service**
```php
class EASportsApiService
{
    public function __construct(
        private HttpClient $httpClient,
        private CacheManager $cache
    ) {}

    public function getClubData(string $clubId, string $platform): ?array
    {
        $cacheKey = "ea_club_{$platform}_{$clubId}";
        
        return $this->cache->remember($cacheKey, 300, function () use ($clubId, $platform) {
            try {
                $response = $this->httpClient->get("/clubs/{$platform}/{$clubId}");
                return $response->successful() ? $response->json() : null;
            } catch (Exception $e) {
                Log::error('EA Sports API error', ['error' => $e->getMessage()]);
                return null;
            }
        });
    }

    public function getMatchResults(string $clubId, string $platform, int $limit = 20): array
    {
        try {
            $response = $this->httpClient->get("/clubs/{$platform}/{$clubId}/matches", [
                'limit' => $limit,
                'sort' => 'desc'
            ]);

            if ($response->successful()) {
                return $this->transformMatchData($response->json());
            }

            return [];
        } catch (Exception $e) {
            Log::error('EA Sports API match fetch error', [
                'club_id' => $clubId,
                'platform' => $platform,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    public function syncClubMatches(Club $club): int
    {
        $matches = $this->getMatchResults($club->ea_club_id, $club->platform);
        $syncedCount = 0;

        foreach ($matches as $matchData) {
            if ($this->importMatch($matchData, $club)) {
                $syncedCount++;
            }
        }

        return $syncedCount;
    }

    private function importMatch(array $matchData, Club $club): bool
    {
        // Check if match already exists
        if (Result::where('ea_match_id', $matchData['id'])->exists()) {
            return false;
        }

        DB::beginTransaction();
        
        try {
            // Create match result
            $result = Result::create([
                'ea_match_id' => $matchData['id'],
                'home_club_id' => $this->findOrCreateClub($matchData['home_club'])->id,
                'away_club_id' => $this->findOrCreateClub($matchData['away_club'])->id,
                'home_score' => $matchData['home_score'],
                'away_score' => $matchData['away_score'],
                'match_type' => $matchData['type'] ?? 'league',
                'platform' => $club->platform,
                'played_at' => Carbon::parse($matchData['played_at']),
            ]);

            // Import player statistics
            $this->importPlayerStats($result, $matchData['player_stats']);

            DB::commit();
            return true;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Match import error', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
```

---

## **6. Queue & Job Processing**

### **6.1 Background Job Architecture**
```php
// EA Sports Data Sync Job
class SyncEASportsDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;
    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        private Club $club,
        private string $syncType = 'full'
    ) {}

    public function handle(EASportsApiService $eaService): void
    {
        try {
            match ($this->syncType) {
                'full' => $this->performFullSync($eaService),
                'matches' => $this->syncMatches($eaService),
                'stats' => $this->syncStats($eaService),
                default => throw new InvalidArgumentException("Unknown sync type: {$this->syncType}")
            };

            Log::info('EA Sports sync completed', [
                'club_id' => $this->club->id,
                'sync_type' => $this->syncType
            ]);

        } catch (Exception $e) {
            Log::error('EA Sports sync failed', [
                'club_id' => $this->club->id,
                'sync_type' => $this->syncType,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    private function performFullSync(EASportsApiService $eaService): void
    {
        // Update club information
        $clubData = $eaService->getClubData($this->club->ea_club_id, $this->club->platform);
        if ($clubData) {
            $this->club->update([
                'name' => $clubData['name'],
                'division' => $clubData['division'],
                'points' => $clubData['points'],
            ]);
        }

        // Sync recent matches
        $matchCount = $eaService->syncClubMatches($this->club);
        
        // Update club statistics
        app(ClubService::class)->updateClubStats($this->club);

        // Dispatch player sync jobs for club members
        $this->club->players->each(function (Player $player) {
            SyncPlayerDataJob::dispatch($player)->delay(rand(10, 60));
        });
    }

    public function failed(Throwable $exception): void
    {
        Log::error('EA Sports sync job failed permanently', [
            'club_id' => $this->club->id,
            'sync_type' => $this->syncType,
            'error' => $exception->getMessage()
        ]);

        // Notify administrators
        Notification::route('mail', config('app.admin_email'))
            ->notify(new EASportsSyncFailed($this->club, $exception));
    }
}

// Scheduled Command for Regular Sync
class SyncEASportsDataCommand extends Command
{
    protected $signature = 'ea:sync {--type=full} {--clubs=} {--limit=50}';
    protected $description = 'Sync data from EA Sports API';

    public function handle(): int
    {
        $type = $this->option('type');
        $clubIds = $this->option('clubs') ? explode(',', $this->option('clubs')) : null;
        $limit = (int) $this->option('limit');

        $query = Club::where('is_active', true)
                     ->whereNotNull('ea_club_id');

        if ($clubIds) {
            $query->whereIn('id', $clubIds);
        }

        $clubs = $query->limit($limit)->get();

        $this->info("Starting EA Sports sync for {$clubs->count()} clubs");

        $clubs->each(function (Club $club, int $index) use ($type) {
            // Stagger job dispatch to avoid rate limiting
            $delay = $index * 30; // 30 seconds between each club sync
            
            SyncEASportsDataJob::dispatch($club, $type)->delay($delay);
            
            $this->line("Dispatched sync job for club {$club->name} (delay: {$delay}s)");
        });

        $this->info('All sync jobs dispatched successfully');
        return 0;
    }
}
```

### **6.2 Queue Configuration**
```php
// config/queue.php
return [
    'default' => env('QUEUE_CONNECTION', 'redis'),

    'connections' => [
        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
            'queue' => env('REDIS_QUEUE', 'default'),
            'retry_after' => 300,
            'block_for' => null,
            'after_commit' => false,
        ],
    ],

    'batching' => [
        'database' => env('DB_CONNECTION', 'mysql'),
        'table' => 'job_batches',
    ],

    'failed' => [
        'driver' => env('QUEUE_FAILED_DRIVER', 'database-uuids'),
        'database' => env('DB_CONNECTION', 'mysql'),
        'table' => 'failed_jobs',
    ],
];

// Horizon configuration
return [
    'environments' => [
        'production' => [
            'supervisor-1' => [
                'connection' => 'redis',
                'queue' => ['default', 'high', 'low'],
                'balance' => 'auto',
                'processes' => 10,
                'tries' => 3,
                'nice' => 0,
            ],
        ],

        'local' => [
            'supervisor-1' => [
                'connection' => 'redis',
                'queue' => ['default'],
                'balance' => 'simple',
                'processes' => 3,
                'tries' => 3,
                'nice' => 0,
            ],
        ],
    ],

    'defaults' => [
        'supervisor-1' => [
            'connection' => 'redis',
            'queue' => ['default'],
            'balance' => 'simple',
            'processes' => 1,
            'tries' => 3,
            'nice' => 0,
        ],
    ],
];
```

---

## **7. Event-Driven Architecture**

### **7.1 Domain Events**
```php
// Player Events
class PlayerCreated
{
    public function __construct(public Player $player) {}
}

class PlayerUpdated
{
    public function __construct(
        public Player $player,
        public array $changes
    ) {}
}

class PlayerTransferred
{
    public function __construct(
        public Player $player,
        public Club $toClub,
        public ?Club $fromClub = null
    ) {}
}

class PlayerAchievementEarned
{
    public function __construct(
        public Player $player,
        public Achievement $achievement,
        public ?Result $match = null
    ) {}
}

// Match Events
class MatchImported
{
    public function __construct(public Result $result) {}
}

class ClubStatsUpdated
{
    public function __construct(public Club $club) {}
}
```

### **7.2 Event Listeners**
```php
// Player Statistics Listener
class UpdatePlayerStatsListener
{
    public function __construct(
        private StatisticsService $statisticsService
    ) {}

    public function handle(MatchImported $event): void
    {
        $result = $event->result;
        
        // Update stats for all players in the match
        $result->playerStats->each(function (ResultPlayerStat $playerStat) {
            $this->statisticsService->updatePlayerStatistics(
                $playerStat->player,
                $playerStat->toArray()
            );
        });
    }
}

// Community Ranking Listener
class UpdateCommunityRankingsListener
{
    public function __construct(
        private RankingService $rankingService
    ) {}

    public function handle(ClubStatsUpdated $event): void
    {
        // Update club rankings
        $this->rankingService->updateClubRankings($event->club->platform);
    }

    public function handle(PlayerUpdated $event): void
    {
        // Update player rankings if relevant stats changed
        if ($this->hasRankingRelevantChanges($event->changes)) {
            $this->rankingService->updatePlayerRankings(
                $event->player->position,
                $event->player->platform
            );
        }
    }

    private function hasRankingRelevantChanges(array $changes): bool
    {
        $rankingFields = ['overall_rating', 'goals', 'assists', 'clean_sheets'];
        return !empty(array_intersect(array_keys($changes), $rankingFields));
    }
}

// Notification Listener
class SendNotificationsListener
{
    public function handle(PlayerAchievementEarned $event): void
    {
        // Notify player of achievement
        $event->player->user?->notify(
            new AchievementEarnedNotification($event->achievement)
        );

        // Notify club members if it's a significant achievement
        if ($event->achievement->is_significant) {
            $this->notifyClubMembers($event->player, $event->achievement);
        }
    }

    public function handle(PlayerTransferred $event): void
    {
        // Notify old club members
        if ($event->fromClub) {
            $this->notifyClubTransfer($event->fromClub, $event->player, 'left');
        }

        // Notify new club members
        $this->notifyClubTransfer($event->toClub, $event->player, 'joined');
    }
}
```

---

## **8. Caching Strategy**

### **8.1 Application Caching**
```php
// Cached Repository Pattern
class CachedPlayerRepository extends PlayerRepository
{
    public function __construct(
        private PlayerRepository $repository,
        private CacheManager $cache
    ) {}

    public function find(int $id): ?Player
    {
        return $this->cache->remember(
            "player:{$id}",
            3600, // 1 hour
            fn() => $this->repository->find($id)
        );
    }

    public function getLeaderboard(string $position, string $platform, int $limit = 50): Collection
    {
        return $this->cache->remember(
            "leaderboard:players:{$position}:{$platform}:{$limit}",
            1800, // 30 minutes
            fn() => $this->repository->getLeaderboard($position, $platform, $limit)
        );
    }

    public function searchWithFilters(string $term, array $filters, int $perPage): LengthAwarePaginator
    {
        $cacheKey = 'search:players:' . md5(serialize([
            'term' => $term,
            'filters' => $filters,
            'per_page' => $perPage,
            'page' => request('page', 1)
        ]));

        return $this->cache->remember(
            $cacheKey,
            600, // 10 minutes
            fn() => $this->repository->searchWithFilters($term, $filters, $perPage)
        );
    }

    public function invalidatePlayerCache(Player $player): void
    {
        $this->cache->forget("player:{$player->id}");
        $this->cache->tags(['players', "player:{$player->id}"])->flush();
    }
}

// Model Events for Cache Invalidation
class Player extends Model
{
    protected static function booted(): void
    {
        static::updated(function (Player $player) {
            app(CachedPlayerRepository::class)->invalidatePlayerCache($player);
        });

        static::deleted(function (Player $player) {
            app(CachedPlayerRepository::class)->invalidatePlayerCache($player);
        });
    }
}
```

### **8.2 API Response Caching**
```php
// API Response Cache Middleware
class CacheApiResponse
{
    public function handle(Request $request, Closure $next, int $ttl = 3600): Response
    {
        if (!$this->shouldCache($request)) {
            return $next($request);
        }

        $cacheKey = $this->generateCacheKey($request);

        return Cache::remember($cacheKey, $ttl, function () use ($next, $request) {
            return $next($request);
        });
    }

    private function shouldCache(Request $request): bool
    {
        return $request->isMethod('GET') 
            && !$request->user() 
            && !$request->hasAny(['page', 'limit', 'search']);
    }

    private function generateCacheKey(Request $request): string
    {
        return 'api:' . md5($request->fullUrl());
    }
}

// Usage in routes
Route::get('/public/leaderboards', [PublicController::class, 'leaderboards'])
     ->middleware('cache.api:1800'); // Cache for 30 minutes
```

---

## **9. Security Implementation**

### **9.1 Authentication & Authorization**
```php
// Custom Authorization Policies
class PlayerPolicy
{
    public function view(User $user, Player $player): bool
    {
        return $player->is_active || $user->can('view-inactive-players');
    }

    public function update(User $user, Player $player): bool
    {
        return $user->id === $player->user_id || $user->hasRole('admin');
    }

    public function claim(User $user, Player $player): bool
    {
        return $player->user_id === null 
            && $player->ea_player_id !== null
            && $user->canClaimPlayers();
    }

    public function delete(User $user, Player $player): bool
    {
        return $user->hasRole('admin') || 
               ($user->id === $player->user_id && $player->canBeDeleted());
    }
}

class ClubPolicy
{
    public function viewMembers(User $user, Club $club): bool
    {
        return $club->is_public || 
               $user->isMemberOf($club) || 
               $user->hasRole('admin');
    }

    public function manage(User $user, Club $club): bool
    {
        return $user->isManagerOf($club) || $user->hasRole('admin');
    }

    public function join(User $user, Club $club): bool
    {
        return $club->accepts_new_members 
            && !$user->isMemberOf($club)
            && $user->hasVerifiedEmail();
    }
}
```

### **9.2 Rate Limiting**
```php
// API Rate Limiting
class RateLimitServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('search', function (Request $request) {
            return Limit::perMinute(20)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('ea-sync', function (Request $request) {
            return Limit::perHour(10)->by($request->user()?->id ?: $request->ip());
        });
    }
}

// Usage in routes
Route::middleware(['throttle:search'])->group(function () {
    Route::get('/search/players', [SearchController::class, 'players']);
    Route::get('/search/clubs', [SearchController::class, 'clubs']);
});
```

### **9.3 Input Validation**
```php
// Form Request Validation
class CreatePlayerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create-player');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'position' => ['required', Rule::in(['GK','CB','LB','RB','CDM','CM','CAM','LM','RM','LW','RW','ST'])],
            'ea_player_id' => ['nullable', 'string', 'unique:players,ea_player_id'],
            'platform' => ['required', Rule::in(['pc', 'playstation', 'xbox', 'switch'])],
            'nationality' => ['nullable', 'string', 'size:3'],
            'height' => ['nullable', 'integer', 'min:150', 'max:220'],
            'weight' => ['nullable', 'integer', 'min:50', 'max:120'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Player name is required',
            'name.min' => 'Player name must be at least 2 characters',
            'position.required' => 'Player position is required',
            'position.in' => 'Invalid player position',
            'ea_player_id.unique' => 'This EA Player ID is already registered',
            'platform.required' => 'Platform is required',
            'nationality.size' => 'Nationality must be a 3-letter country code',
        ];
    }
}

// Custom Validation Rules
class ValidEAPlayerIdRule implements Rule
{
    public function passes($attribute, $value): bool
    {
        if (empty($value)) {
            return true; // Allow null/empty values
        }

        // Validate EA Player ID format
        return preg_match('/^[A-Z0-9]{8,16}$/', $value);
    }

    public function message(): string
    {
        return 'The EA Player ID format is invalid.';
    }
}
```

---

## **10. Testing Architecture**

### **10.1 Unit Testing**
```php
// Service Layer Tests
class PlayerServiceTest extends TestCase
{
    use RefreshDatabase;

    private PlayerService $playerService;
    private PlayerRepository $playerRepository;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->playerRepository = Mockery::mock(PlayerRepository::class);
        $this->playerService = new PlayerService(
            $this->playerRepository,
            app(EASportsApiService::class),
            app(StatisticsService::class)
        );
    }

    public function test_creates_player_with_valid_data(): void
    {
        $requestData = [
            'name' => 'Test Player',
            'position' => 'ST',
            'platform' => 'pc',
        ];

        $this->playerRepository
            ->shouldReceive('create')
            ->once()
            ->with($requestData)
            ->andReturn(Player::factory()->make($requestData));

        $player = $this->playerService->createPlayer(
            new CreatePlayerRequest($requestData)
        );

        $this->assertInstanceOf(Player::class, $player);
        $this->assertEquals('Test Player', $player->name);
    }

    public function test_syncs_player_with_ea_api_when_ea_id_provided(): void
    {
        $player = Player::factory()->create(['ea_player_id' => 'EA123456']);
        
        $eaApiService = Mockery::mock(EASportsApiService::class);
        $eaApiService->shouldReceive('getPlayerData')
                    ->with('EA123456')
                    ->once()
                    ->andReturn(['overall' => 85, 'height' => 180]);

        $this->app->instance(EASportsApiService::class, $eaApiService);

        $this->playerService->syncPlayerWithEA($player);

        $this->assertEquals(85, $player->fresh()->overall_rating);
    }
}

// Model Tests
class PlayerTest extends TestCase
{
    use RefreshDatabase;

    public function test_player_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $player = Player::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $player->user);
        $this->assertEquals($user->id, $player->user->id);
    }

    public function test_player_has_attributes(): void
    {
        $player = Player::factory()->create();
        PlayerAttribute::factory()->create(['player_id' => $player->id]);

        $this->assertInstanceOf(PlayerAttribute::class, $player->attributes);
    }

    public function test_calculates_average_rating_correctly(): void
    {
        $player = Player::factory()->create();
        $club = Club::factory()->create();
        $result = Result::factory()->create();

        // Create multiple match stats
        ResultPlayerStat::factory()->create([
            'player_id' => $player->id,
            'result_id' => $result->id,
            'rating' => 8.5
        ]);
        
        ResultPlayerStat::factory()->create([
            'player_id' => $player->id,
            'result_id' => $result->id,
            'rating' => 7.5
        ]);

        $averageRating = $player->matchStats()->avg('rating');
        $this->assertEquals(8.0, $averageRating);
    }
}
```

### **10.2 Feature Testing**
```php
// API Feature Tests
class PlayerApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_retrieve_player_list(): void
    {
        $players = Player::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/players');

        $response->assertOk()
                ->assertJsonCount(3, 'data')
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id', 'name', 'position', 'overall_rating',
                            'platform', 'created_at', 'updated_at'
                        ]
                    ],
                    'meta' => ['total', 'per_page', 'current_page']
                ]);
    }

    public function test_can_search_players_by_name(): void
    {
        Player::factory()->create(['name' => 'John Doe']);
        Player::factory()->create(['name' => 'Jane Smith']);

        $response = $this->getJson('/api/v1/players?search=john');

        $response->assertOk()
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.name', 'John Doe');
    }

    public function test_requires_authentication_to_create_player(): void
    {
        $playerData = [
            'name' => 'Test Player',
            'position' => 'ST',
            'platform' => 'pc',
        ];

        $response = $this->postJson('/api/v1/players', $playerData);

        $response->assertUnauthorized();
    }

    public function test_authenticated_user_can_create_player(): void
    {
        $user = User::factory()->create();
        $playerData = [
            'name' => 'Test Player',
            'position' => 'ST',
            'platform' => 'pc',
        ];

        $response = $this->actingAs($user)
                        ->postJson('/api/v1/players', $playerData);

        $response->assertCreated()
                ->assertJsonPath('data.name', 'Test Player')
                ->assertJsonPath('data.position', 'ST');

        $this->assertDatabaseHas('players', $playerData);
    }
}
```

---

**Document Maintenance:**
- Backend Structure Version: 1.0  
- Last Updated: June 14, 2025  
- Next Review: September 14, 2025  
- Maintained By: Backend Development Team