# Pro Clubs v3 Database Structure

This document provides an overview of the database structure for the Pro Clubs v3 application, inferred from the model definitions.

## Core Tables

### Users
- **Table**: `users`
- **Description**: Stores user account information
- **Fields**:
  - `id` (primary key)
  - `name` (string)
  - `email` (string, unique)
  - `email_verified_at` (timestamp, nullable)
  - `password` (string)
  - `remember_token` (string, nullable)
  - `created_at` (timestamp)
  - `updated_at` (timestamp)
- **Relationships**:
  - One-to-one with `Player` (via `player_id`)

### Clubs
- **Table**: `clubs`
- **Description**: Stores information about Pro Clubs teams
- **Fields**:
  - `id` (primary key)
  - `name` (string)
  - `ea_club_id` (string/integer)
  - `platform` (string)
  - `badge_id` (integer)
  - `last_scanned_at` (timestamp)
  - `skill_rating` (integer/float)
  - `created_at` (timestamp)
  - `updated_at` (timestamp)
- **Relationships**:
  - One-to-many with `Player`
  - One-to-many with `Result` (as home_club)
  - One-to-many with `Result` (as away_club)
  - One-to-many with `PlayerHistory`

### Players
- **Table**: `players`
- **Description**: Stores information about individual players
- **Fields**:
  - `id` (primary key)
  - `club_id` (foreign key to `clubs.id`)
  - `name` (string)
  - `ea_player_id` (string/integer)
  - `platform` (string)
  - `attributes` (json/text)
  - `position_type` (string)
  - `ea_pro_position` (string)
  - `ea_pro_height` (string/integer)
  - `ea_pro_nationality` (string)
  - `ea_pro_overall` (integer)
  - `ea_pro_fav_position` (string)
  - `prev_goals` (integer)
  - `performance_trend` (string/json)
  - `is_cheater` (boolean)
  - `cheater_reason` (string, nullable)
  - `flagged_at` (timestamp, nullable)
  - `created_at` (timestamp)
  - `updated_at` (timestamp)
- **Relationships**:
  - Belongs to `Club`
  - One-to-one with `PlayerAttribute`
  - One-to-many with `ResultPlayerStat`
  - One-to-one with `User`

### Results
- **Table**: `results`
- **Description**: Stores match results between clubs
- **Fields**:
  - `id` (primary key)
  - `ea_result_id` (string/integer)
  - `platform` (string)
  - `match_type` (string)
  - `home_club_id` (foreign key to `clubs.id`)
  - `away_club_id` (foreign key to `clubs.id`)
  - `home_goals` (integer)
  - `away_goals` (integer)
  - `outcome` (string)
  - `media` (json/text)
  - `properties` (json/text)
  - `match_date` (timestamp)
  - `created_at` (timestamp)
  - `updated_at` (timestamp)
- **Relationships**:
  - Belongs to `Club` (as home_club)
  - Belongs to `Club` (as away_club)
  - One-to-one with `ResultMatchStat`
  - One-to-many with `ResultPlayerStat`

## Player-Related Tables

### PlayerAttributes
- **Table**: `player_attributes`
- **Description**: Stores detailed attributes for players
- **Fields**:
  - `id` (primary key)
  - `player_id` (foreign key to `players.id`)
  - `fav_position` (string)
  - Various player attributes (acceleration, aggression, agility, etc.)
  - `created_at` (timestamp)
  - `updated_at` (timestamp)
- **Relationships**:
  - Belongs to `Player`

### PlayerTransfers
- **Table**: `player_transfers`
- **Description**: Tracks player transfers between clubs
- **Fields**:
  - `id` (primary key)
  - `player_id` (foreign key to `players.id`)
  - `from_club_id` (foreign key to `clubs.id`)
  - `to_club_id` (foreign key to `clubs.id`)
  - `platform` (string)
  - `transfer_date` (timestamp)
  - `last_played_at` (timestamp)
  - `first_played_at` (timestamp)
  - `is_active` (boolean)
  - `notes` (text)
  - `created_at` (timestamp)
  - `updated_at` (timestamp)
- **Relationships**:
  - Belongs to `Player`
  - Belongs to `Club` (as from_club)
  - Belongs to `Club` (as to_club)

### PlayerHistories
- **Table**: `player_histories`
- **Description**: Tracks historical data for players
- **Fields**:
  - `id` (primary key)
  - `club_id` (foreign key to `clubs.id`)
  - Other fields not explicitly defined in the models
  - `created_at` (timestamp)
  - `updated_at` (timestamp)
- **Relationships**:
  - Belongs to `Club`

## Result-Related Tables

### ResultPlayerStats
- **Table**: `result_player_stats`
- **Description**: Stores player statistics for each match
- **Fields**:
  - `id` (primary key)
  - `result_id` (foreign key to `results.id`)
  - `player_id` (foreign key to `players.id`)
  - `club_id` (foreign key to `clubs.id`)
  - `platform` (string)
  - `goals` (integer)
  - `assists` (integer)
  - `wins` (integer)
  - `losses` (integer)
  - `draws` (integer)
  - `mom` (integer) - Man of the Match
  - `rating` (float)
  - `shots` (integer)
  - `tackles_made` (integer)
  - `tackles_attempted` (integer)
  - `passes_made` (integer)
  - `passes_attempted` (integer)
  - `red_cards` (integer)
  - `clean_sheets_gk` (integer)
  - `clean_sheets_def` (integer)
  - `clean_sheets_any` (integer)
  - `goals_conceded` (integer)
  - `saves` (integer)
  - `position` (string)
  - `week_number` (integer)
  - `realtime_game` (integer/float)
  - `realtime_idle` (integer/float)
  - `created_at` (timestamp)
  - `updated_at` (timestamp)
- **Relationships**:
  - Belongs to `Result`
  - Belongs to `Player`
  - Belongs to `Club`

### ResultMatchStats
- **Table**: `result_match_stats`
- **Description**: Stores match statistics for each result
- **Fields**:
  - `id` (primary key)
  - `result_id` (foreign key to `results.id`)
  - Other fields not explicitly defined in the models
  - `created_at` (timestamp)
  - `updated_at` (timestamp)
- **Relationships**:
  - Belongs to `Result`

## Other Tables

### Achievements
- **Table**: `achievements`
- **Description**: Stores achievement definitions
- **Fields**: Not explicitly defined in the models

### PlayerAchievements
- **Table**: `player_achievements`
- **Description**: Links players to their achievements
- **Fields**: Not explicitly defined in the models

### PlayerPoints
- **Table**: `player_points`
- **Description**: Tracks points earned by players
- **Fields**: Not explicitly defined in the models

### PlayerSnapshots
- **Table**: `player_snapshots`
- **Description**: Stores point-in-time snapshots of player data
- **Fields**: Not explicitly defined in the models

### Countries
- **Table**: `countries`
- **Description**: Stores country information
- **Fields**: Not explicitly defined in the models

### Messages
- **Table**: `messages`
- **Description**: Stores messages between users or system notifications
- **Fields**: Not explicitly defined in the models

### CommunityClubRankings
- **Table**: `community_club_rankings`
- **Description**: Stores rankings for clubs in the community
- **Fields**: Not explicitly defined in the models

### CommunityPlayerRankings
- **Table**: `community_player_rankings`
- **Description**: Stores rankings for players in the community
- **Fields**: Not explicitly defined in the models

### UserClubs
- **Table**: `user_clubs`
- **Description**: Links users to clubs (possibly for management/membership)
- **Fields**: Not explicitly defined in the models

### UserFavouriteClubs
- **Table**: `user_favourite_clubs`
- **Description**: Tracks clubs that users have marked as favorites
- **Fields**: Not explicitly defined in the models

### SocialProviders
- **Table**: `social_providers`
- **Description**: Stores OAuth provider information for social logins
- **Fields**: Not explicitly defined in the models

## Laravel System Tables

### Password Reset Tokens
- **Table**: `password_reset_tokens`
- **Description**: Stores tokens for password reset functionality
- **Fields**:
  - `email` (string, primary key)
  - `token` (string)
  - `created_at` (timestamp, nullable)

### Sessions
- **Table**: `sessions`
- **Description**: Stores session information
- **Fields**:
  - `id` (string, primary key)
  - `user_id` (foreign key to `users.id`, nullable)
  - `ip_address` (string, nullable)
  - `user_agent` (text, nullable)
  - `payload` (longtext)
  - `last_activity` (integer)

### Cache
- **Table**: `cache`
- **Description**: Stores cached data
- **Fields**: Standard Laravel cache table fields

### Jobs
- **Table**: `jobs`
- **Description**: Stores queued jobs
- **Fields**: Standard Laravel jobs table fields

### Failed Jobs
- **Table**: `failed_jobs`
- **Description**: Stores failed jobs
- **Fields**: Standard Laravel failed jobs table fields

### Personal Access Tokens
- **Table**: `personal_access_tokens`
- **Description**: Stores API tokens for authentication
- **Fields**: Standard Laravel personal access tokens table fields

## Relationships Diagram

The main relationships in the database can be summarized as follows:

- User ↔ Player (one-to-one)
- Club ↔ Player (one-to-many)
- Club ↔ Result (one-to-many, as both home and away club)
- Player ↔ PlayerAttribute (one-to-one)
- Player ↔ ResultPlayerStat (one-to-many)
- Result ↔ ResultMatchStat (one-to-one)
- Result ↔ ResultPlayerStat (one-to-many)
- Player ↔ PlayerTransfer (one-to-many)
- Club ↔ PlayerTransfer (one-to-many, as both from and to club)
