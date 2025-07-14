# Pro Clubs Companion v3 - Database Schema

## Overview
This document outlines the complete database schema for the Pro Clubs Companion v3 application, designed to store EA FC 25 Pro Clubs data, user information, match results, player statistics, and AI-powered analysis results.

## Design Principles
- **Scalability**: Designed to handle large volumes of match data and player statistics
- **Performance**: Optimized with proper indexing for fast queries
- **Data Integrity**: Foreign key constraints ensure referential integrity
- **Extensibility**: Flexible schema supports future EA FC updates
- **AI Integration**: Native support for AI-analyzed data from Prism system

---

## Core Business Tables

### 1. clubs
**Purpose**: Store Pro Clubs team information
```sql
CREATE TABLE clubs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    ea_club_id VARCHAR(255) UNIQUE,
    platform ENUM('xbox', 'playstation', 'pc') NOT NULL,
    badge_id VARCHAR(255),
    skill_rating INT UNSIGNED DEFAULT 0,
    reputation ENUM('hometown_heroes', 'regional_recognition', 'national_fame', 'international_status', 'world_renowned') DEFAULT 'hometown_heroes',
    best_playoff_finish VARCHAR(255),
    highest_division VARCHAR(255),
    league_appearances INT UNSIGNED DEFAULT 0,
    playoff_appearances INT UNSIGNED DEFAULT 0,
    rush_record VARCHAR(255),
    total_rush_goals INT UNSIGNED DEFAULT 0,
    emblem_description TEXT,
    emblem_colors VARCHAR(255),
    estimated_real_club VARCHAR(255),
    last_scanned_at TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_clubs_ea_club_id (ea_club_id),
    INDEX idx_clubs_platform (platform),
    INDEX idx_clubs_skill_rating (skill_rating),
    INDEX idx_clubs_active (is_active),
    UNIQUE KEY unique_ea_club_platform (ea_club_id, platform)
);
```

### 2. players
**Purpose**: Individual player profiles and Pro information
```sql
CREATE TABLE players (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    club_id BIGINT UNSIGNED,
    user_id BIGINT UNSIGNED NULL,
    name VARCHAR(255) NOT NULL,
    ea_player_id VARCHAR(255),
    platform ENUM('xbox', 'playstation', 'pc') NOT NULL,
    
    -- Pro Player Details
    ea_pro_position VARCHAR(10),
    ea_pro_height VARCHAR(10),
    ea_pro_nationality VARCHAR(3),
    ea_pro_overall INT UNSIGNED,
    ea_pro_fav_position VARCHAR(10),
    
    -- Career Statistics
    appearances INT UNSIGNED DEFAULT 0,
    goals INT UNSIGNED DEFAULT 0,
    assists INT UNSIGNED DEFAULT 0,
    clean_sheets INT UNSIGNED DEFAULT 0,
    player_of_match INT UNSIGNED DEFAULT 0,
    red_cards INT UNSIGNED DEFAULT 0,
    pass_completion_rate DECIMAL(5,2),
    shots_on_target_rate DECIMAL(5,2),
    
    -- Performance Tracking
    prev_goals INT UNSIGNED DEFAULT 0,
    performance_trend ENUM('improving', 'declining', 'stable') DEFAULT 'stable',
    vpr_rating DECIMAL(3,1) DEFAULT 0.0,
    
    -- Moderation
    is_cheater BOOLEAN DEFAULT false,
    cheater_reason TEXT,
    flagged_at TIMESTAMP NULL,
    
    -- Metadata
    last_played_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (club_id) REFERENCES clubs(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_players_club (club_id),
    INDEX idx_players_ea_player_id (ea_player_id),
    INDEX idx_players_platform (platform),
    INDEX idx_players_overall (ea_pro_overall),
    INDEX idx_players_position (ea_pro_position),
    INDEX idx_players_vpr (vpr_rating),
    UNIQUE KEY unique_ea_player_platform (ea_player_id, platform)
);
```

### 3. results
**Purpose**: Match results and metadata
```sql
CREATE TABLE results (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ea_result_id VARCHAR(255) UNIQUE,
    platform ENUM('xbox', 'playstation', 'pc') NOT NULL,
    match_type ENUM('league', 'playoff', 'friendly', 'rush') NOT NULL,
    
    -- Teams
    home_club_id BIGINT UNSIGNED NOT NULL,
    away_club_id BIGINT UNSIGNED NOT NULL,
    
    -- Score
    home_goals INT UNSIGNED NOT NULL,
    away_goals INT UNSIGNED NOT NULL,
    outcome ENUM('home_win', 'away_win', 'draw') NOT NULL,
    
    -- Match Details
    match_time VARCHAR(10), -- e.g., "92:11"
    match_date TIMESTAMP NOT NULL,
    
    -- Media & Metadata
    media JSON, -- Screenshots, videos, etc.
    properties JSON, -- Additional match properties
    ai_analyzed BOOLEAN DEFAULT false,
    ai_analysis_data JSON, -- Prism analysis results
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (home_club_id) REFERENCES clubs(id) ON DELETE CASCADE,
    FOREIGN KEY (away_club_id) REFERENCES clubs(id) ON DELETE CASCADE,
    INDEX idx_results_ea_result_id (ea_result_id),
    INDEX idx_results_platform (platform),
    INDEX idx_results_match_type (match_type),
    INDEX idx_results_home_club (home_club_id),
    INDEX idx_results_away_club (away_club_id),
    INDEX idx_results_match_date (match_date),
    INDEX idx_results_outcome (outcome),
    CHECK (home_club_id != away_club_id)
);
```

### 4. result_player_stats
**Purpose**: Individual player statistics per match
```sql
CREATE TABLE result_player_stats (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    result_id BIGINT UNSIGNED NOT NULL,
    player_id BIGINT UNSIGNED NOT NULL,
    club_id BIGINT UNSIGNED NOT NULL,
    platform ENUM('xbox', 'playstation', 'pc') NOT NULL,
    
    -- Basic Stats
    goals INT UNSIGNED DEFAULT 0,
    assists INT UNSIGNED DEFAULT 0,
    match_rating DECIMAL(3,1) DEFAULT 0.0,
    
    -- Performance Counters
    wins INT UNSIGNED DEFAULT 0,
    losses INT UNSIGNED DEFAULT 0,
    draws INT UNSIGNED DEFAULT 0,
    
    -- Detailed Stats
    shots INT UNSIGNED DEFAULT 0,
    shots_on_target INT UNSIGNED DEFAULT 0,
    passes INT UNSIGNED DEFAULT 0,
    passes_completed INT UNSIGNED DEFAULT 0,
    tackles INT UNSIGNED DEFAULT 0,
    tackles_won INT UNSIGNED DEFAULT 0,
    interceptions INT UNSIGNED DEFAULT 0,
    blocks INT UNSIGNED DEFAULT 0,
    saves INT UNSIGNED DEFAULT 0,
    clearances INT UNSIGNED DEFAULT 0,
    
    -- Cards & Fouls
    yellow_cards INT UNSIGNED DEFAULT 0,
    red_cards INT UNSIGNED DEFAULT 0,
    fouls_committed INT UNSIGNED DEFAULT 0,
    fouls_suffered INT UNSIGNED DEFAULT 0,
    
    -- Position & Role
    position VARCHAR(10),
    formation_position VARCHAR(10),
    minutes_played INT UNSIGNED DEFAULT 90,
    
    -- Metadata
    player_of_match BOOLEAN DEFAULT false,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (result_id) REFERENCES results(id) ON DELETE CASCADE,
    FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE CASCADE,
    FOREIGN KEY (club_id) REFERENCES clubs(id) ON DELETE CASCADE,
    INDEX idx_rps_result (result_id),
    INDEX idx_rps_player (player_id),
    INDEX idx_rps_club (club_id),
    INDEX idx_rps_rating (match_rating),
    INDEX idx_rps_goals (goals),
    INDEX idx_rps_assists (assists),
    UNIQUE KEY unique_result_player (result_id, player_id)
);
```

### 5. result_match_stats
**Purpose**: Team-level match statistics
```sql
CREATE TABLE result_match_stats (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    result_id BIGINT UNSIGNED NOT NULL,
    
    -- Home Team Stats
    home_possession DECIMAL(5,2),
    home_shots INT UNSIGNED DEFAULT 0,
    home_shots_on_target INT UNSIGNED DEFAULT 0,
    home_passes INT UNSIGNED DEFAULT 0,
    home_pass_accuracy DECIMAL(5,2),
    home_tackles INT UNSIGNED DEFAULT 0,
    home_fouls INT UNSIGNED DEFAULT 0,
    home_corners INT UNSIGNED DEFAULT 0,
    home_offsides INT UNSIGNED DEFAULT 0,
    
    -- Away Team Stats
    away_possession DECIMAL(5,2),
    away_shots INT UNSIGNED DEFAULT 0,
    away_shots_on_target INT UNSIGNED DEFAULT 0,
    away_passes INT UNSIGNED DEFAULT 0,
    away_pass_accuracy DECIMAL(5,2),
    away_tackles INT UNSIGNED DEFAULT 0,
    away_fouls INT UNSIGNED DEFAULT 0,
    away_corners INT UNSIGNED DEFAULT 0,
    away_offsides INT UNSIGNED DEFAULT 0,
    
    -- Advanced Analytics
    expected_goals_home DECIMAL(4,2),
    expected_goals_away DECIMAL(4,2),
    ball_recovery_time_home INT UNSIGNED,
    ball_recovery_time_away INT UNSIGNED,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (result_id) REFERENCES results(id) ON DELETE CASCADE,
    UNIQUE KEY unique_result_stats (result_id),
    INDEX idx_rms_result (result_id)
);
```

### 6. player_attributes
**Purpose**: Detailed FIFA/EA FC player attributes
```sql
CREATE TABLE player_attributes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    player_id BIGINT UNSIGNED NOT NULL,
    
    -- Physical Attributes
    pace INT UNSIGNED DEFAULT 0,
    acceleration INT UNSIGNED DEFAULT 0,
    sprint_speed INT UNSIGNED DEFAULT 0,
    agility INT UNSIGNED DEFAULT 0,
    balance INT UNSIGNED DEFAULT 0,
    jumping INT UNSIGNED DEFAULT 0,
    stamina INT UNSIGNED DEFAULT 0,
    strength INT UNSIGNED DEFAULT 0,
    
    -- Technical Attributes
    ball_control INT UNSIGNED DEFAULT 0,
    dribbling INT UNSIGNED DEFAULT 0,
    first_touch INT UNSIGNED DEFAULT 0,
    passing INT UNSIGNED DEFAULT 0,
    short_passing INT UNSIGNED DEFAULT 0,
    long_passing INT UNSIGNED DEFAULT 0,
    curve INT UNSIGNED DEFAULT 0,
    crossing INT UNSIGNED DEFAULT 0,
    
    -- Shooting Attributes
    shooting INT UNSIGNED DEFAULT 0,
    finishing INT UNSIGNED DEFAULT 0,
    shot_power INT UNSIGNED DEFAULT 0,
    long_shots INT UNSIGNED DEFAULT 0,
    volleys INT UNSIGNED DEFAULT 0,
    penalties INT UNSIGNED DEFAULT 0,
    
    -- Defending Attributes
    defending INT UNSIGNED DEFAULT 0,
    interceptions INT UNSIGNED DEFAULT 0,
    heading_accuracy INT UNSIGNED DEFAULT 0,
    marking INT UNSIGNED DEFAULT 0,
    standing_tackle INT UNSIGNED DEFAULT 0,
    sliding_tackle INT UNSIGNED DEFAULT 0,
    
    -- Mental Attributes
    aggression INT UNSIGNED DEFAULT 0,
    reactions INT UNSIGNED DEFAULT 0,
    attacking_positioning INT UNSIGNED DEFAULT 0,
    defensive_positioning INT UNSIGNED DEFAULT 0,
    vision INT UNSIGNED DEFAULT 0,
    composure INT UNSIGNED DEFAULT 0,
    
    -- Goalkeeper Attributes
    gk_diving INT UNSIGNED DEFAULT 0,
    gk_handling INT UNSIGNED DEFAULT 0,
    gk_kicking INT UNSIGNED DEFAULT 0,
    gk_positioning INT UNSIGNED DEFAULT 0,
    gk_reflexes INT UNSIGNED DEFAULT 0,
    
    -- Skill Ratings
    weak_foot INT UNSIGNED DEFAULT 1,
    skill_moves INT UNSIGNED DEFAULT 1,
    overall_rating INT UNSIGNED DEFAULT 0,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE CASCADE,
    UNIQUE KEY unique_player_attributes (player_id),
    INDEX idx_pa_overall (overall_rating),
    INDEX idx_pa_pace (pace),
    INDEX idx_pa_shooting (shooting),
    INDEX idx_pa_passing (passing),
    INDEX idx_pa_defending (defending)
);
```

### 7. player_transfers
**Purpose**: Track player movement between clubs
```sql
CREATE TABLE player_transfers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    player_id BIGINT UNSIGNED NOT NULL,
    from_club_id BIGINT UNSIGNED NULL,
    to_club_id BIGINT UNSIGNED NOT NULL,
    platform ENUM('xbox', 'playstation', 'pc') NOT NULL,
    
    transfer_date TIMESTAMP NOT NULL,
    last_played_at TIMESTAMP NULL,
    first_played_at TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT true,
    transfer_type ENUM('join', 'leave', 'trade') DEFAULT 'join',
    
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE CASCADE,
    FOREIGN KEY (from_club_id) REFERENCES clubs(id) ON DELETE SET NULL,
    FOREIGN KEY (to_club_id) REFERENCES clubs(id) ON DELETE CASCADE,
    INDEX idx_pt_player (player_id),
    INDEX idx_pt_from_club (from_club_id),
    INDEX idx_pt_to_club (to_club_id),
    INDEX idx_pt_transfer_date (transfer_date),
    INDEX idx_pt_active (is_active)
);
```

---

## AI & Analysis Tables

### 8. player_detailed_stats
**Purpose**: AI-extracted detailed player statistics from Prism analysis
```sql
CREATE TABLE player_detailed_stats (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    result_id BIGINT UNSIGNED,
    player_id BIGINT UNSIGNED,
    club_id BIGINT UNSIGNED,
    
    -- Identification
    player_name VARCHAR(255) NOT NULL,
    position VARCHAR(10),
    match_rating DECIMAL(3,1),
    
    -- Basic Performance
    goals INT UNSIGNED DEFAULT 0,
    assists INT UNSIGNED DEFAULT 0,
    
    -- Detailed Statistics (JSON for flexibility)
    summary_stats JSON,
    shooting_stats JSON,
    passing_stats JSON,
    defending_stats JSON,
    possession_stats JSON,
    
    -- AI Analysis Metadata
    analysis_type VARCHAR(50) NOT NULL,
    ai_provider VARCHAR(20) NOT NULL,
    ai_model VARCHAR(50) NOT NULL,
    confidence_score DECIMAL(3,2),
    extracted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Source Information
    source_image_hash VARCHAR(64),
    cache_key VARCHAR(255),
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (result_id) REFERENCES results(id) ON DELETE SET NULL,
    FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE SET NULL,
    FOREIGN KEY (club_id) REFERENCES clubs(id) ON DELETE SET NULL,
    INDEX idx_pds_result (result_id),
    INDEX idx_pds_player (player_id),
    INDEX idx_pds_club (club_id),
    INDEX idx_pds_rating (match_rating),
    INDEX idx_pds_analysis_type (analysis_type),
    INDEX idx_pds_extracted_at (extracted_at),
    INDEX idx_pds_source_hash (source_image_hash)
);
```

### 9. prism_analysis_cache
**Purpose**: Cache AI analysis results for performance
```sql
CREATE TABLE prism_analysis_cache (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cache_key VARCHAR(255) NOT NULL UNIQUE,
    image_hash VARCHAR(64) NOT NULL,
    analysis_type VARCHAR(50) NOT NULL,
    ai_provider VARCHAR(20) NOT NULL,
    ai_model VARCHAR(50) NOT NULL,
    
    -- Analysis Results
    analysis_data JSON NOT NULL,
    metadata JSON,
    
    -- Cache Management
    hit_count INT UNSIGNED DEFAULT 0,
    last_accessed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_pac_cache_key (cache_key),
    INDEX idx_pac_image_hash (image_hash),
    INDEX idx_pac_analysis_type (analysis_type),
    INDEX idx_pac_expires_at (expires_at),
    INDEX idx_pac_last_accessed (last_accessed_at)
);
```

---

## User & Community Tables

### 10. users (Enhanced)
**Purpose**: User accounts with Pro Clubs integration
```sql
-- This table exists but needs these additional columns:
ALTER TABLE users ADD COLUMN IF NOT EXISTS country_id BIGINT UNSIGNED NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS player_id BIGINT UNSIGNED NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS has_mic BOOLEAN DEFAULT false;
ALTER TABLE users ADD COLUMN IF NOT EXISTS properties JSON;
ALTER TABLE users ADD COLUMN IF NOT EXISTS suspended_at TIMESTAMP NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS avatar VARCHAR(255);
ALTER TABLE users ADD COLUMN IF NOT EXISTS preferred_platform ENUM('xbox', 'playstation', 'pc');

-- Add foreign keys
ALTER TABLE users ADD FOREIGN KEY (country_id) REFERENCES countries(id) ON DELETE SET NULL;
ALTER TABLE users ADD FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE SET NULL;

-- Add indexes
ALTER TABLE users ADD INDEX idx_users_country (country_id);
ALTER TABLE users ADD INDEX idx_users_player (player_id);
ALTER TABLE users ADD INDEX idx_users_platform (preferred_platform);
```

### 11. countries
**Purpose**: Country reference data
```sql
CREATE TABLE countries (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    code_alpha2 CHAR(2) NOT NULL UNIQUE,
    code_alpha3 CHAR(3) NOT NULL UNIQUE,
    flag_emoji CHAR(2),
    is_active BOOLEAN DEFAULT true,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_countries_code2 (code_alpha2),
    INDEX idx_countries_code3 (code_alpha3),
    INDEX idx_countries_active (is_active)
);
```

### 12. user_clubs
**Purpose**: User-club associations and permissions
```sql
CREATE TABLE user_clubs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    club_id BIGINT UNSIGNED NOT NULL,
    role ENUM('owner', 'admin', 'member', 'viewer') DEFAULT 'member',
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT true,
    
    permissions JSON, -- Specific permissions for this club
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (club_id) REFERENCES clubs(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_club (user_id, club_id),
    INDEX idx_uc_user (user_id),
    INDEX idx_uc_club (club_id),
    INDEX idx_uc_role (role),
    INDEX idx_uc_active (is_active)
);
```

### 13. user_favourite_clubs
**Purpose**: User's favorite clubs for quick access
```sql
CREATE TABLE user_favourite_clubs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    club_id BIGINT UNSIGNED NOT NULL,
    order_index INT UNSIGNED DEFAULT 0,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (club_id) REFERENCES clubs(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_favourite_club (user_id, club_id),
    INDEX idx_ufc_user (user_id),
    INDEX idx_ufc_club (club_id),
    INDEX idx_ufc_order (order_index)
);
```

---

## Community & Rankings Tables

### 14. community_club_rankings
**Purpose**: Weekly/monthly club rankings and leaderboards
```sql
CREATE TABLE community_club_rankings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    club_id BIGINT UNSIGNED NOT NULL,
    platform ENUM('xbox', 'playstation', 'pc') NOT NULL,
    
    -- Ranking Period
    period_type ENUM('weekly', 'monthly', 'seasonal') NOT NULL,
    period_start DATE NOT NULL,
    period_end DATE NOT NULL,
    
    -- Rankings
    position INT UNSIGNED NOT NULL,
    previous_position INT UNSIGNED,
    skill_rating INT UNSIGNED DEFAULT 0,
    vp_points INT UNSIGNED DEFAULT 0,
    games_played INT UNSIGNED DEFAULT 0,
    
    -- Performance Metrics
    wins INT UNSIGNED DEFAULT 0,
    losses INT UNSIGNED DEFAULT 0,
    draws INT UNSIGNED DEFAULT 0,
    goals_for INT UNSIGNED DEFAULT 0,
    goals_against INT UNSIGNED DEFAULT 0,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (club_id) REFERENCES clubs(id) ON DELETE CASCADE,
    INDEX idx_ccr_club (club_id),
    INDEX idx_ccr_platform (platform),
    INDEX idx_ccr_period (period_type, period_start, period_end),
    INDEX idx_ccr_position (position),
    INDEX idx_ccr_skill_rating (skill_rating),
    UNIQUE KEY unique_club_period (club_id, platform, period_type, period_start)
);
```

### 15. community_player_rankings
**Purpose**: Player leaderboards and VPR rankings
```sql
CREATE TABLE community_player_rankings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    player_id BIGINT UNSIGNED NOT NULL,
    platform ENUM('xbox', 'playstation', 'pc') NOT NULL,
    
    -- Ranking Period
    period_type ENUM('weekly', 'monthly', 'seasonal') NOT NULL,
    period_start DATE NOT NULL,
    period_end DATE NOT NULL,
    
    -- Rankings by Category
    overall_rank INT UNSIGNED,
    position_rank INT UNSIGNED,
    vpr_rating DECIMAL(3,1) DEFAULT 0.0,
    
    -- Performance Metrics
    appearances INT UNSIGNED DEFAULT 0,
    goals INT UNSIGNED DEFAULT 0,
    assists INT UNSIGNED DEFAULT 0,
    clean_sheets INT UNSIGNED DEFAULT 0,
    player_of_match INT UNSIGNED DEFAULT 0,
    average_rating DECIMAL(3,1) DEFAULT 0.0,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE CASCADE,
    INDEX idx_cpr_player (player_id),
    INDEX idx_cpr_platform (platform),
    INDEX idx_cpr_period (period_type, period_start, period_end),
    INDEX idx_cpr_overall_rank (overall_rank),
    INDEX idx_cpr_position_rank (position_rank),
    INDEX idx_cpr_vpr (vpr_rating),
    UNIQUE KEY unique_player_period (player_id, platform, period_type, period_start)
);
```

---

## Supporting Tables

### 16. achievements
**Purpose**: Achievement definitions and metadata
```sql
CREATE TABLE achievements (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    category ENUM('goals', 'assists', 'matches', 'rating', 'special') NOT NULL,
    
    -- Requirements
    requirement_type ENUM('total', 'single_match', 'streak', 'average') NOT NULL,
    requirement_value INT UNSIGNED NOT NULL,
    requirement_criteria JSON, -- Additional criteria
    
    -- Rewards
    badge_icon VARCHAR(255),
    badge_color VARCHAR(7), -- Hex color
    points_reward INT UNSIGNED DEFAULT 0,
    
    -- Metadata
    is_active BOOLEAN DEFAULT true,
    difficulty ENUM('bronze', 'silver', 'gold', 'platinum') DEFAULT 'bronze',
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_achievements_category (category),
    INDEX idx_achievements_difficulty (difficulty),
    INDEX idx_achievements_active (is_active)
);
```

### 17. player_achievements
**Purpose**: Track player achievement progress and completions
```sql
CREATE TABLE player_achievements (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    player_id BIGINT UNSIGNED NOT NULL,
    achievement_id BIGINT UNSIGNED NOT NULL,
    
    -- Progress Tracking
    current_progress INT UNSIGNED DEFAULT 0,
    max_progress INT UNSIGNED NOT NULL,
    is_completed BOOLEAN DEFAULT false,
    completed_at TIMESTAMP NULL,
    
    -- Associated Data
    result_id BIGINT UNSIGNED NULL, -- Match where achievement was earned
    metadata JSON, -- Additional achievement data
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE CASCADE,
    FOREIGN KEY (achievement_id) REFERENCES achievements(id) ON DELETE CASCADE,
    FOREIGN KEY (result_id) REFERENCES results(id) ON DELETE SET NULL,
    UNIQUE KEY unique_player_achievement (player_id, achievement_id),
    INDEX idx_pa_player (player_id),
    INDEX idx_pa_achievement (achievement_id),
    INDEX idx_pa_completed (is_completed),
    INDEX idx_pa_completed_at (completed_at)
);
```

### 18. player_points
**Purpose**: Point-based reward system for players
```sql
CREATE TABLE player_points (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    player_id BIGINT UNSIGNED NOT NULL,
    
    -- Point Balances
    total_points INT UNSIGNED DEFAULT 0,
    available_points INT UNSIGNED DEFAULT 0,
    spent_points INT UNSIGNED DEFAULT 0,
    
    -- Lifetime Statistics
    points_from_goals INT UNSIGNED DEFAULT 0,
    points_from_assists INT UNSIGNED DEFAULT 0,
    points_from_wins INT UNSIGNED DEFAULT 0,
    points_from_rating INT UNSIGNED DEFAULT 0,
    points_from_achievements INT UNSIGNED DEFAULT 0,
    
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE CASCADE,
    UNIQUE KEY unique_player_points (player_id),
    INDEX idx_pp_total_points (total_points),
    INDEX idx_pp_available_points (available_points)
);
```

### 19. messages
**Purpose**: In-app messaging system
```sql
CREATE TABLE messages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sender_id BIGINT UNSIGNED,
    recipient_id BIGINT UNSIGNED,
    club_id BIGINT UNSIGNED NULL, -- For club-wide messages
    
    subject VARCHAR(255),
    body TEXT NOT NULL,
    message_type ENUM('direct', 'club', 'system', 'announcement') DEFAULT 'direct',
    
    -- Status
    is_read BOOLEAN DEFAULT false,
    read_at TIMESTAMP NULL,
    priority ENUM('low', 'normal', 'high', 'urgent') DEFAULT 'normal',
    
    -- Metadata
    metadata JSON, -- Attachments, references, etc.
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (recipient_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (club_id) REFERENCES clubs(id) ON DELETE SET NULL,
    INDEX idx_messages_sender (sender_id),
    INDEX idx_messages_recipient (recipient_id),
    INDEX idx_messages_club (club_id),
    INDEX idx_messages_type (message_type),
    INDEX idx_messages_read (is_read),
    INDEX idx_messages_created (created_at)
);
```

---

## Data Archive Tables

### 20. player_snapshots
**Purpose**: Point-in-time player data for historical analysis
```sql
CREATE TABLE player_snapshots (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    player_id BIGINT UNSIGNED NOT NULL,
    club_id BIGINT UNSIGNED,
    
    -- Snapshot Data
    snapshot_date DATE NOT NULL,
    overall_rating INT UNSIGNED,
    goals INT UNSIGNED DEFAULT 0,
    assists INT UNSIGNED DEFAULT 0,
    appearances INT UNSIGNED DEFAULT 0,
    average_rating DECIMAL(3,1) DEFAULT 0.0,
    vpr_rating DECIMAL(3,1) DEFAULT 0.0,
    
    -- Calculated Metrics
    form_trend ENUM('improving', 'declining', 'stable') DEFAULT 'stable',
    performance_score DECIMAL(5,2),
    
    -- Full Data Backup
    full_data JSON, -- Complete player state at snapshot time
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE CASCADE,
    FOREIGN KEY (club_id) REFERENCES clubs(id) ON DELETE SET NULL,
    INDEX idx_ps_player (player_id),
    INDEX idx_ps_club (club_id),
    INDEX idx_ps_snapshot_date (snapshot_date),
    INDEX idx_ps_overall_rating (overall_rating),
    UNIQUE KEY unique_player_snapshot_date (player_id, snapshot_date)
);
```

### 21. json_result_archives
**Purpose**: Archive raw JSON data from EA APIs
```sql
CREATE TABLE json_result_archives (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    result_id BIGINT UNSIGNED,
    
    -- Source Information
    source_api VARCHAR(100) NOT NULL,
    api_endpoint VARCHAR(255),
    request_params JSON,
    
    -- Raw Data
    raw_json_data JSON NOT NULL,
    processed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_hash VARCHAR(64), -- For deduplication
    
    -- Metadata
    api_response_time INT UNSIGNED, -- Response time in milliseconds
    data_size INT UNSIGNED, -- Size in bytes
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (result_id) REFERENCES results(id) ON DELETE SET NULL,
    INDEX idx_jra_result (result_id),
    INDEX idx_jra_source_api (source_api),
    INDEX idx_jra_processed_at (processed_at),
    INDEX idx_jra_data_hash (data_hash)
);
```

---

## Database Views

### Common Query Views
```sql
-- Player Performance Summary
CREATE VIEW player_performance_summary AS
SELECT 
    p.id,
    p.name,
    p.ea_pro_overall,
    p.ea_pro_position,
    c.name as club_name,
    COUNT(rps.id) as matches_played,
    AVG(rps.match_rating) as avg_rating,
    SUM(rps.goals) as total_goals,
    SUM(rps.assists) as total_assists,
    p.vpr_rating
FROM players p
LEFT JOIN clubs c ON p.club_id = c.id
LEFT JOIN result_player_stats rps ON p.id = rps.player_id
WHERE p.is_active = true
GROUP BY p.id;

-- Club Rankings Summary  
CREATE VIEW club_rankings_summary AS
SELECT 
    c.id,
    c.name,
    c.platform,
    c.skill_rating,
    COUNT(DISTINCT p.id) as player_count,
    COUNT(DISTINCT r.id) as matches_played,
    AVG(CASE WHEN r.home_club_id = c.id THEN r.home_goals ELSE r.away_goals END) as avg_goals_for,
    AVG(CASE WHEN r.home_club_id = c.id THEN r.away_goals ELSE r.home_goals END) as avg_goals_against
FROM clubs c
LEFT JOIN players p ON c.id = p.club_id
LEFT JOIN results r ON c.id IN (r.home_club_id, r.away_club_id)
WHERE c.is_active = true
GROUP BY c.id;

-- Recent Match Results
CREATE VIEW recent_match_results AS
SELECT 
    r.id,
    r.match_date,
    hc.name as home_club,
    ac.name as away_club,
    r.home_goals,
    r.away_goals,
    r.outcome,
    r.match_type,
    r.platform
FROM results r
JOIN clubs hc ON r.home_club_id = hc.id
JOIN clubs ac ON r.away_club_id = ac.id
ORDER BY r.match_date DESC;
```

---

## Database Indexes Strategy

### Performance Optimization
```sql
-- Composite indexes for common query patterns
CREATE INDEX idx_results_club_date ON results(home_club_id, away_club_id, match_date);
CREATE INDEX idx_player_stats_performance ON result_player_stats(player_id, match_rating, goals, assists);
CREATE INDEX idx_players_club_position ON players(club_id, ea_pro_position, ea_pro_overall);

-- Full-text search indexes
CREATE FULLTEXT INDEX idx_clubs_name_search ON clubs(name);
CREATE FULLTEXT INDEX idx_players_name_search ON players(name);

-- JSON indexes for AI analysis data (MySQL 8.0+)
CREATE INDEX idx_ai_analysis_type ON player_detailed_stats((JSON_EXTRACT(summary_stats, '$.type')));
CREATE INDEX idx_ai_confidence ON player_detailed_stats(confidence_score);
```

---

## Data Relationships Summary

```
Users ←→ Players ←→ Clubs
  ↓         ↓        ↓
UserClubs  Stats   Results
  ↓         ↓        ↓
Favorites Rankings Analysis
```

### Key Relationships:
- **Users** can be associated with **Players** (1:1 optional)
- **Players** belong to **Clubs** (Many:1)
- **Users** can follow multiple **Clubs** via **UserClubs**
- **Results** connect two **Clubs** and contain **PlayerStats**
- **AI Analysis** enhances **Results** with detailed insights
- **Rankings** provide leaderboards for **Clubs** and **Players**

---

## Migration Strategy

### Phase 1: Core Tables
1. `countries` - Reference data
2. `clubs` - Core business entity
3. `players` - Core business entity
4. `user_clubs`, `user_favourite_clubs` - User associations

### Phase 2: Match Data
1. `results` - Match results
2. `result_player_stats` - Player performance
3. `result_match_stats` - Team statistics
4. `player_attributes` - Player details

### Phase 3: Community Features
1. `achievements`, `player_achievements` - Achievement system
2. `community_club_rankings`, `community_player_rankings` - Leaderboards
3. `messages` - Communication system
4. `player_points` - Reward system

### Phase 4: Advanced Features
1. `player_transfers` - Transfer tracking
2. `prism_analysis_cache` - AI caching
3. `player_snapshots` - Historical data
4. `json_result_archives` - Data archiving

---

## Data Integrity Rules

### Business Rules
1. **Platform Consistency**: All related records must have matching platform values
2. **Club Membership**: Players can only belong to one active club per platform
3. **Match Validation**: Home and away clubs must be different
4. **Rating Bounds**: Match ratings must be between 0.0 and 10.0
5. **Attribute Limits**: Player attributes must be between 0 and 99
6. **Transfer Logic**: Active transfers must have valid to_club_id

### Constraints
```sql
-- Rating constraints
ALTER TABLE result_player_stats ADD CONSTRAINT chk_match_rating 
    CHECK (match_rating >= 0.0 AND match_rating <= 10.0);

-- Attribute constraints  
ALTER TABLE player_attributes ADD CONSTRAINT chk_attributes_range
    CHECK (pace BETWEEN 0 AND 99 AND shooting BETWEEN 0 AND 99);

-- Platform consistency
ALTER TABLE results ADD CONSTRAINT chk_platform_consistency
    CHECK (platform IN ('xbox', 'playstation', 'pc'));
```

---

This comprehensive database schema provides the foundation for a scalable, performant Pro Clubs companion application with full AI integration capabilities and robust community features.