# API Endpoints Reference - Pro Clubs

## Base Information

**Base URL**: `/api/`  
**Authentication**: Laravel Sanctum (Bearer Token)  
**Rate Limiting**: 60 requests/minute for public EA API endpoints  

## Authentication Endpoints

### Public Authentication

#### Register
- **POST** `/auth/register`
- **Controller**: `Auth\RegisterController`
- **Authentication**: None required
- **Request Body**:
  ```json
  {
    "name": "string",
    "email": "string",
    "password": "string",
    "password_confirmation": "string"
  }
  ```
- **Response**: Returns access token

#### Login
- **POST** `/auth/login`
- **Controller**: `Auth\LoginController`
- **Authentication**: None required
- **Request Body**:
  ```json
  {
    "email": "string",
    "password": "string",
    "remember": "boolean (optional)"
  }
  ```
- **Response**: Returns access token

### Authenticated Authentication

#### Logout
- **POST** `/auth/logout`
- **Controller**: `Auth\LogoutController`
- **Authentication**: Required (sanctum)
- **Response**: Invalidates current token

## User Management Endpoints

### Current User
- **GET** `/user`
- **Authentication**: Required (sanctum)
- **Response**: `UserResource`

### Users CRUD (Full Orion Resource)
- **GET** `/users` - List all users
- **POST** `/users` - Create new user
- **GET** `/users/{id}` - Get specific user
- **PUT/PATCH** `/users/{id}` - Update user
- **DELETE** `/users/{id}` - Delete user
- **Controller**: `UserController`
- **Authentication**: Required (sanctum)
- **Filters**: `id`, `name`, `email`, `created_at`
- **Sorting**: `id`, `name`, `email`, `created_at`

### User Results
- **GET** `/user/results`
- **Controller**: `UserResultController`
- **Authentication**: Required (sanctum)
- **Description**: Get results for current user's clubs

## Club Management Endpoints

### Clubs CRUD (Full Orion Resource)
- **GET** `/clubs` - List all clubs
- **POST** `/clubs` - Create new club
- **GET** `/clubs/{id}` - Get specific club
- **PUT/PATCH** `/clubs/{id}` - Update club
- **DELETE** `/clubs/{id}` - Delete club
- **Controller**: `ClubController`
- **Authentication**: Required (sanctum)

### Club-specific Endpoints

#### Get Club Players
- **GET** `/clubs/{club}/players`
- **Controller**: `ClubController@getPlayers`
- **Authentication**: Required (sanctum)

#### Get Club Form
- **GET** `/clubs/{club}/form`
- **Controller**: `ClubFormController`
- **Authentication**: Required (sanctum)

## Player Management Endpoints

### Players CRUD (Full Orion Resource)
- **GET** `/players` - List all players
- **POST** `/players` - Create new player
- **GET** `/players/{id}` - Get specific player
- **PUT/PATCH** `/players/{id}` - Update player
- **DELETE** `/players/{id}` - Delete player
- **Controller**: `PlayerController`
- **Authentication**: Required (sanctum)
- **Query Parameters**: `exclude_cheaters` (boolean)

### Player-specific Endpoints

#### Player Summary (AI-Generated)
- **GET** `/players/{player}/summary`
- **Controller**: `PlayerController@getSummary`
- **Authentication**: Required (sanctum)
- **Description**: AI-generated player summary

#### Player Estimated Value
- **GET** `/players/{player}/estimated-value`
- **Controller**: `PlayerController@getEstimatedValue`
- **Authentication**: Required (sanctum)

#### Player Card Generation
- **GET** `/players/{player}/card`
- **Controller**: `PlayerCardController`
- **Authentication**: Required (sanctum)
- **Response**: Player card image

#### Player History
- **GET** `/players/{player}/history`
- **Controller**: `PlayerHistoryController`
- **Authentication**: Required (sanctum)

#### Player Form
- **GET** `/players/{player}/form`
- **Controller**: `PlayerFormController`
- **Authentication**: Required (sanctum)

#### Position Grouping
- **GET** `/players/{player}/position-grouping`
- **Controller**: `PlayerPositionGroupingController`
- **Authentication**: Required (sanctum)

#### Club Position Grouping
- **GET** `/players/{player}/position-grouping-club`
- **Controller**: `PlayerPositionGroupingClubController`
- **Authentication**: Required (sanctum)

#### Attribute Search
- **GET** `/players/attributes/search`
- **Controller**: `AttributeSearchController`
- **Authentication**: Required (sanctum)

## Player Attributes Endpoints

### Player Attributes CRUD (Full Orion Resource)
- **GET** `/player-attributes` - List all player attributes
- **POST** `/player-attributes` - Create new player attributes
- **GET** `/player-attributes/{id}` - Get specific player attributes
- **PUT/PATCH** `/player-attributes/{id}` - Update player attributes
- **DELETE** `/player-attributes/{id}` - Delete player attributes
- **Controller**: `PlayerAttributeController`
- **Authentication**: Required (sanctum)

## Results Management Endpoints

### Results CRUD (Full Orion Resource)
- **GET** `/results` - List all results
- **POST** `/results` - Create new result
- **GET** `/results/{ea_result_id}` - Get specific result (uses ea_result_id as key)
- **PUT/PATCH** `/results/{ea_result_id}` - Update result
- **DELETE** `/results/{ea_result_id}` - Delete result
- **Controller**: `ResultController`
- **Authentication**: Required (sanctum)
- **Filters**: `id`, `platform`, `home_club_id`, `away_club_id`, `match_type`, `created_at`
- **Sorting**: `id`, `platform`, `match_date`, `created_at`
- **Scopes**: `recent`

## JSON Result Archives Endpoints

### JSON Result Archives CRUD (Full Orion Resource)
- **GET** `/json-result-archives` - List all archived results
- **POST** `/json-result-archives` - Create new archived result
- **GET** `/json-result-archives/{ea_result_id}` - Get specific archived result
- **PUT/PATCH** `/json-result-archives/{ea_result_id}` - Update archived result
- **DELETE** `/json-result-archives/{ea_result_id}` - Delete archived result
- **Controller**: `JsonResultArchiveController`
- **Authentication**: Required (sanctum)

### Match Summary (AI-Generated)
- **GET** `/json-result-archives/{result}/summary`
- **Controller**: `JsonResultArchiveController@getSummary`
- **Authentication**: Required (sanctum)
- **Description**: AI-generated match summary

## User Favourite Clubs Endpoints

### User Favourite Clubs CRUD (Full Orion Resource)
- **GET** `/user-favourite-clubs` - List user's favourite clubs
- **POST** `/user-favourite-clubs` - Add club to favourites
- **GET** `/user-favourite-clubs/{id}` - Get specific favourite club
- **PUT/PATCH** `/user-favourite-clubs/{id}` - Update favourite club
- **DELETE** `/user-favourite-clubs/{id}` - Remove from favourites
- **Controller**: `UserFavouriteClubController`
- **Authentication**: Required (sanctum)
- **Filters**: `id`, `user_id`, `club_id`, `created_at`
- **Sorting**: `id`, `user_id`, `club_id`, `created_at`
- **Note**: `user_id` is automatically set to current authenticated user

### User Favourite Club Relations
- **GET** `/users/{user}/favourite-clubs` - Get user's favourite clubs
- **POST** `/users/{user}/favourite-clubs` - Attach favourite club to user
- **DELETE** `/users/{user}/favourite-clubs/{club}` - Detach favourite club from user
- **Controller**: `UserFavouriteClubRelationController`
- **Authentication**: Required (sanctum)

## Community Rankings Endpoints

### Club Rankings
- **GET** `/community-rankings/clubs/{platform?}`
- **Controller**: `CommunityRankingClubController`
- **Authentication**: Required (sanctum)
- **Parameters**: `platform` (optional) - Filter by platform

### Player Rankings
- **GET** `/community-rankings/players/{platform?}`
- **Controller**: `CommunityRankingPlayerController`
- **Authentication**: Required (sanctum)
- **Parameters**: `platform` (optional) - Filter by platform

### Player Points Rankings
- **GET** `/community-rankings/players/points/{periodType}/{periodNumber?}/{platform?}/{clubId?}`
- **Controller**: `CommunityRankingPlayerPointController`
- **Authentication**: Required (sanctum)
- **Parameters**:
  - `periodType` (required) - Type of period
  - `periodNumber` (optional) - Specific period number
  - `platform` (optional) - Filter by platform
  - `clubId` (optional) - Filter by club

### Player Position Rankings
- **GET** `/community-rankings/players/positions/{periodType}/{periodNumber?}/{platform?}/{clubId?}`
- **Controller**: `CommunityRankingPlayerPositionController`
- **Authentication**: Required (sanctum)
- **Parameters**: Same as player points rankings

### Best XI & Weekly Players (TODO)
- **GET** `/community-rankings/best-xi` - Get best XI *(Not implemented)*
- **GET** `/community-rankings/weekly-players` - Get weekly players *(Not implemented)*

## EA API Integration Endpoints

**Note**: These endpoints are public but rate-limited (60 requests per minute)

### Club Information
- **GET** `/ea/clubs/{platform}/{eaClubId}`
- **Controller**: `EaController@getClub`
- **Authentication**: None required
- **Rate Limit**: 60/minute

### Match Data
- **GET** `/ea/matches/{matchType}/{platform}/{eaClubId}`
- **Controller**: `EaController@getMatches`
- **Authentication**: None required
- **Rate Limit**: 60/minute

### Statistics
- **GET** `/ea/career/{platform}/{eaClubId}` - Career statistics
- **GET** `/ea/members/{platform}/{eaClubId}` - Member statistics
- **GET** `/ea/overall-stats/{platform}/{eaClubId}` - Overall statistics
- **GET** `/ea/playoff-achievements/{platform}/{eaClubId}` - Playoff achievements
- **Controller**: `EaController`
- **Authentication**: None required
- **Rate Limit**: 60/minute each

### Settings & Search
- **GET** `/ea/settings/{platform}` - Get EA settings for platform
- **GET** `/ea/search/{platform}/{clubName}` - Search clubs by name
- **Controller**: `EaController`
- **Authentication**: None required
- **Rate Limit**: 60/minute each

### Club Comparisons
- **GET** `/ea/clubs/compare-career-stats/{platform}/{eaClubId1}/{eaClubId2}`
- **GET** `/ea/clubs/compare-member-stats/{platform}/{eaClubId1}/{eaClubId2}`
- **GET** `/ea/clubs/compare-clubs-info/{platform}/{eaClubId1}/{eaClubId2}`
- **GET** `/ea/clubs/compare-overall-stats/{platform}/{eaClubId1}/{eaClubId2}`
- **Controller**: `EaController`
- **Authentication**: None required
- **Rate Limit**: 60/minute each

## Debug Endpoint

- **GET** `/debug`
- **Controller**: `DebugController`
- **Authentication**: None required

## Authentication & Authorization Notes

### Authentication Method
- **Laravel Sanctum** with bearer tokens
- Include token in header: `Authorization: Bearer {token}`

### Token Management
- Login provides access token
- Remember me option affects token expiration
- Logout invalidates current token

### Authorization
- Most controllers use `DisableAuthorization` trait
- Some controllers implement role-based permissions (Admin role)
- User-specific data is automatically filtered by authenticated user

## Request/Response Patterns

### Standard Responses
- All API responses use consistent format via `ApiResponseHelpers` trait
- Resources are used for data transformation
- Collection resources for list endpoints
- Single resources for individual items

### Query Features (via Orion package)
- **Filtering**: Available on specified fields for each resource
- **Sorting**: Available on specified fields for each resource
- **Pagination**: Built-in for all list endpoints
- **Scopes**: Available for complex queries where specified

### Error Responses
Standard HTTP status codes are used:
- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `500` - Server Error

## Rate Limiting

- **EA API Endpoints**: 60 requests per minute
- **General API Endpoints**: Standard Laravel rate limiting applies