# Pro Clubs Companion v3 - API Design

## Overview
This document defines the complete API specification for the Pro Clubs Companion v3 application, providing communication rules between the frontend (React/TypeScript) and backend (Laravel) systems. The API supports EA FC 25 Pro Clubs data management, AI-powered analytics, community features, and real-time match tracking.

## API Architecture

### Base Configuration
```
Base URL: https://proclubs-companion-v3.test/api
API Version: v1
Authentication: Laravel Sanctum (Bearer tokens)
Content-Type: application/json
Rate Limiting: 60 requests/minute (general), 10 requests/minute (EA endpoints)
```

### Response Format Standards
All API responses follow a consistent structure:

**Success Response:**
```json
{
    "success": true,
    "data": {...},
    "meta": {
        "timestamp": "2025-06-15T10:30:00Z",
        "version": "v1",
        "cached": false
    }
}
```

**Error Response:**
```json
{
    "success": false,
    "message": "Error description",
    "errors": {...},
    "meta": {
        "timestamp": "2025-06-15T10:30:00Z",
        "version": "v1",
        "error_code": "VALIDATION_FAILED"
    }
}
```

**Paginated Response:**
```json
{
    "success": true,
    "data": [...],
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 5,
        "per_page": 15,
        "to": 15,
        "total": 67
    },
    "links": {
        "first": "...",
        "last": "...",
        "prev": null,
        "next": "..."
    }
}
```

---

## Authentication Endpoints

### POST /api/auth/register
**Purpose:** Register a new user account

**Request:**
```json
{
    "name": "string|required|max:255",
    "email": "string|required|email|unique:users",
    "password": "string|required|min:8|confirmed",
    "password_confirmation": "string|required",
    "country_id": "integer|optional|exists:countries,id",
    "preferred_platform": "enum|optional|in:xbox,playstation,pc",
    "has_mic": "boolean|optional"
}
```

**Response (201 Created):**
```json
{
    "success": true,
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "preferred_platform": "xbox",
            "has_mic": true,
            "created_at": "2025-06-15T10:30:00Z"
        },
        "token": "1|abc123def456...",
        "token_expires_at": "2025-07-15T10:30:00Z"
    }
}
```

### POST /api/auth/login
**Purpose:** Authenticate user and return access token

**Request:**
```json
{
    "email": "string|required|email",
    "password": "string|required",
    "remember": "boolean|optional"
}
```

**Response (200 OK):**
```json
{
    "success": true,
    "data": {
        "user": {...},
        "token": "2|xyz789abc123...",
        "token_expires_at": "2025-07-15T10:30:00Z"
    }
}
```

### POST /api/auth/logout
**Purpose:** Revoke current access token
**Authentication:** Required
**Response (200 OK):**
```json
{
    "success": true,
    "message": "Successfully logged out"
}
```

---

## Core Resource Endpoints

### Clubs API

#### GET /api/clubs
**Purpose:** List clubs with filtering and pagination

**Query Parameters:**
```
?platform=xbox,playstation,pc
?search=string (club name search)
?skill_rating_min=integer
?skill_rating_max=integer
?is_active=boolean
?sort=name,skill_rating,created_at
?order=asc,desc
?page=integer
?per_page=integer (max 50)
```

**Response (200 OK):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Manchester United FC",
            "ea_club_id": "123456",
            "platform": "xbox",
            "skill_rating": 2450,
            "reputation": "world_renowned",
            "badge_id": "man_utd_001",
            "player_count": 11,
            "last_scanned_at": "2025-06-15T09:00:00Z",
            "is_active": true,
            "emblem_description": "Red shield with lion crest",
            "emblem_colors": "Red and gold",
            "estimated_real_club": "Manchester United"
        }
    ],
    "meta": {...}
}
```

#### POST /api/clubs
**Purpose:** Create a new club
**Authentication:** Required

**Request:**
```json
{
    "name": "string|required|max:255",
    "ea_club_id": "string|optional|unique:clubs,ea_club_id,NULL,id,platform,{platform}",
    "platform": "enum|required|in:xbox,playstation,pc",
    "badge_id": "string|optional",
    "skill_rating": "integer|optional|min:0|max:5000"
}
```

#### GET /api/clubs/{id}
**Purpose:** Get specific club details

**Response (200 OK):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "Manchester United FC",
        "ea_club_id": "123456",
        "platform": "xbox",
        "skill_rating": 2450,
        "reputation": "world_renowned",
        "statistics": {
            "total_matches": 245,
            "wins": 180,
            "losses": 45,
            "draws": 20,
            "goals_for": 620,
            "goals_against": 280,
            "win_percentage": 73.47
        },
        "recent_form": [
            {"result": "W", "score": "3-1", "opponent": "Liverpool FC"},
            {"result": "W", "score": "2-0", "opponent": "Chelsea FC"},
            {"result": "L", "score": "1-2", "opponent": "Arsenal FC"}
        ],
        "players": [...],
        "recent_matches": [...]
    }
}
```

#### PUT /api/clubs/{id}
**Purpose:** Update club information
**Authentication:** Required (club admin)

#### DELETE /api/clubs/{id}
**Purpose:** Delete club
**Authentication:** Required (club owner)

#### GET /api/clubs/{id}/players
**Purpose:** Get all players in a club

**Query Parameters:**
```
?position=GK,DEF,MID,ATT
?overall_min=integer
?overall_max=integer
?is_active=boolean
?sort=name,overall,goals,assists
?order=asc,desc
```

#### GET /api/clubs/{id}/form
**Purpose:** Get club's recent form and performance trends

**Response (200 OK):**
```json
{
    "success": true,
    "data": {
        "form_string": "WWLWW",
        "last_5_matches": [...],
        "performance_trend": "improving",
        "statistics": {
            "goals_per_match": 2.8,
            "goals_conceded_per_match": 1.2,
            "win_rate_last_10": 80,
            "clean_sheet_percentage": 40
        }
    }
}
```

### Players API

#### GET /api/players
**Purpose:** List players with advanced filtering

**Query Parameters:**
```
?club_id=integer
?platform=xbox,playstation,pc
?position=GK,RB,CB,LB,CDM,CM,CAM,RM,LM,RW,LW,CF,ST
?overall_min=integer
?overall_max=integer
?nationality=string (3-letter country code)
?search=string (player name)
?vpr_min=decimal
?vpr_max=decimal
?goals_min=integer
?assists_min=integer
?is_active=boolean
?sort=name,overall,goals,assists,vpr_rating
?order=asc,desc
?page=integer
?per_page=integer
```

**Response (200 OK):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Cristiano Ronaldo",
            "ea_player_id": "cr7_001",
            "platform": "xbox",
            "club": {
                "id": 1,
                "name": "Manchester United FC"
            },
            "ea_pro_position": "ST",
            "ea_pro_overall": 95,
            "ea_pro_height": "6'2\"",
            "ea_pro_nationality": "POR",
            "vpr_rating": 9.2,
            "career_stats": {
                "appearances": 145,
                "goals": 98,
                "assists": 32,
                "clean_sheets": 0,
                "player_of_match": 45,
                "red_cards": 2,
                "pass_completion_rate": 85.6,
                "shots_on_target_rate": 68.4
            },
            "performance_trend": "stable",
            "last_played_at": "2025-06-15T08:30:00Z"
        }
    ],
    "meta": {...}
}
```

#### POST /api/players
**Purpose:** Create a new player
**Authentication:** Required

**Request:**
```json
{
    "name": "string|required|max:255",
    "ea_player_id": "string|optional|unique:players,ea_player_id,NULL,id,platform,{platform}",
    "club_id": "integer|required|exists:clubs,id",
    "platform": "enum|required|in:xbox,playstation,pc",
    "ea_pro_position": "string|optional|max:10",
    "ea_pro_height": "string|optional|max:10",
    "ea_pro_nationality": "string|optional|size:3",
    "ea_pro_overall": "integer|optional|min:40|max:99"
}
```

#### GET /api/players/{id}
**Purpose:** Get detailed player information

**Response (200 OK):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "Cristiano Ronaldo",
        "ea_player_id": "cr7_001",
        "platform": "xbox",
        "club": {...},
        "attributes": {
            "pace": 89,
            "shooting": 95,
            "passing": 82,
            "dribbling": 88,
            "defending": 35,
            "physical": 92,
            "overall_rating": 95
        },
        "career_statistics": {...},
        "recent_matches": [...],
        "achievements": [...],
        "playstyles": {
            "playstyles_plus": ["Power Shot+", "Incisive Pass+"],
            "playstyles": ["Finesse Shot", "Technical"]
        }
    }
}
```

#### GET /api/players/{id}/summary
**Purpose:** Get AI-generated player summary

**Response (200 OK):**
```json
{
    "success": true,
    "data": {
        "summary": "Cristiano Ronaldo is an exceptional striker with world-class finishing ability...",
        "strengths": ["Shooting", "Physical presence", "Aerial ability"],
        "weaknesses": ["Defensive contribution", "Pace decline"],
        "playing_style": "Clinical finisher with strong aerial presence",
        "estimated_market_value": 850000,
        "performance_rating": "Excellent",
        "ai_confidence": 0.94,
        "generated_at": "2025-06-15T10:30:00Z"
    }
}
```

#### GET /api/players/{id}/card
**Purpose:** Get player card data for UI display

**Response (200 OK):**
```json
{
    "success": true,
    "data": {
        "player_info": {...},
        "card_design": {
            "card_type": "gold",
            "position_color": "#ff6b35",
            "nation_flag": "portugal.png",
            "club_badge": "man_utd.png"
        },
        "display_stats": {
            "pace": 89,
            "shooting": 95,
            "passing": 82,
            "dribbling": 88,
            "defending": 35,
            "physical": 92
        },
        "special_attributes": {
            "weak_foot": 4,
            "skill_moves": 5
        }
    }
}
```

#### GET /api/players/{id}/history
**Purpose:** Get player's historical performance data

**Query Parameters:**
```
?period=weekly,monthly,seasonal
?limit=integer (max 100)
```

#### GET /api/players/attributes/search
**Purpose:** Advanced player search by attributes

**Request:**
```json
{
    "filters": {
        "pace": {"min": 80, "max": 99},
        "shooting": {"min": 85, "max": 99},
        "overall": {"min": 90, "max": 99}
    },
    "position": ["ST", "CF"],
    "platform": "xbox",
    "club_id": null,
    "limit": 20
}
```

### Results API (Match Results)

#### GET /api/results
**Purpose:** List match results with filtering

**Query Parameters:**
```
?club_id=integer (matches involving this club)
?platform=xbox,playstation,pc
?match_type=league,playoff,friendly,rush
?home_club_id=integer
?away_club_id=integer
?outcome=home_win,away_win,draw
?date_from=date (YYYY-MM-DD)
?date_to=date (YYYY-MM-DD)
?ai_analyzed=boolean
?sort=match_date,home_goals,away_goals
?order=asc,desc
?page=integer
?per_page=integer
```

**Response (200 OK):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "ea_result_id": "match_123456",
            "platform": "xbox",
            "match_type": "league",
            "home_club": {
                "id": 1,
                "name": "Manchester United FC",
                "badge_id": "man_utd_001"
            },
            "away_club": {
                "id": 2,
                "name": "Liverpool FC",
                "badge_id": "liverpool_001"
            },
            "home_goals": 3,
            "away_goals": 1,
            "outcome": "home_win",
            "match_time": "92:11",
            "match_date": "2025-06-15T20:00:00Z",
            "ai_analyzed": true,
            "player_stats_count": 22,
            "match_stats": {...}
        }
    ],
    "meta": {...}
}
```

#### POST /api/results
**Purpose:** Create a new match result
**Authentication:** Required

**Request:**
```json
{
    "ea_result_id": "string|optional|unique:results",
    "platform": "enum|required|in:xbox,playstation,pc",
    "match_type": "enum|required|in:league,playoff,friendly,rush",
    "home_club_id": "integer|required|exists:clubs,id",
    "away_club_id": "integer|required|exists:clubs,id|different:home_club_id",
    "home_goals": "integer|required|min:0",
    "away_goals": "integer|required|min:0",
    "match_time": "string|optional|regex:/^\d{1,3}:\d{2}$/",
    "match_date": "datetime|required",
    "player_stats": "array|optional",
    "match_stats": "object|optional"
}
```

#### GET /api/results/{id}
**Purpose:** Get detailed match result

**Response (200 OK):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "match_info": {...},
        "player_stats": [
            {
                "player": {...},
                "goals": 2,
                "assists": 1,
                "match_rating": 8.7,
                "shots": 6,
                "shots_on_target": 4,
                "passes": 45,
                "passes_completed": 38,
                "position": "ST",
                "minutes_played": 90,
                "player_of_match": true
            }
        ],
        "match_stats": {
            "home_possession": 58.5,
            "away_possession": 41.5,
            "home_shots": 12,
            "away_shots": 8,
            "expected_goals_home": 2.3,
            "expected_goals_away": 1.1
        },
        "ai_analysis": {...}
    }
}
```

---

## AI & Analytics Endpoints

### Prism Image Analysis

#### GET /prism/upload
**Purpose:** Show Prism upload form
**Response:** Inertia.js page with analysis types

#### POST /prism/upload
**Purpose:** Upload and analyze game screenshot

**Request (multipart/form-data):**
```
image: file|required|mimes:jpeg,jpg,png,gif,webp|max:10240
analysis_type: enum|required|in:player-performance,club-leaderboard,pro-leaderboard,match-result,my-pro-overview,my-pro-playstyle,my-pro-preset,playstyle
```

**Response (200 OK):**
```json
{
    "success": true,
    "data": {
        "team_name": "Manchester United FC",
        "featured_player": {
            "name": "Cristiano Ronaldo",
            "overall_rating": 95,
            "match_rating": 8.7
        },
        "player_list": [
            {
                "position": "ST",
                "name": "Cristiano Ronaldo",
                "match_rating": 8.7,
                "goals": 2,
                "assists": 1
            }
        ],
        "detailed_stats_category": "Summary",
        "selected_player_detailed_stats": {...},
        "selected_team_detailed_stats": {...}
    },
    "meta": {
        "cached": false,
        "cache_enabled": true,
        "cache_key": "prism_analysis:abc123...",
        "provider": "gemini",
        "model": "gemini-2.0-flash-lite",
        "analysis_type": "player-performance",
        "image_hash": "sha256:def456...",
        "confidence_score": 0.94
    }
}
```

#### POST /prism/analyze-from-url
**Purpose:** Analyze image from URL

**Request:**
```json
{
    "image_url": "string|required|url",
    "mime_type": "string|optional",
    "analysis_type": "enum|required"
}
```

#### GET /prism/providers
**Purpose:** Get available AI providers and models

**Response (200 OK):**
```json
{
    "success": true,
    "data": {
        "providers": [
            {
                "provider": "gemini",
                "name": "Google Gemini",
                "configured": true,
                "models": ["gemini-2.0-flash", "gemini-1.5-pro"]
            },
            {
                "provider": "openai",
                "name": "OpenAI",
                "configured": true,
                "models": ["gpt-4o", "gpt-4o-mini"]
            }
        ],
        "default_provider": "gemini"
    }
}
```

### Player Analytics

#### GET /api/players/{id}/estimated-value
**Purpose:** Get AI-estimated player market value

**Response (200 OK):**
```json
{
    "success": true,
    "data": {
        "estimated_value": 850000,
        "value_tier": "elite",
        "factors": {
            "overall_rating": 0.3,
            "goals_per_match": 0.25,
            "market_demand": 0.2,
            "age_factor": 0.15,
            "position_scarcity": 0.1
        },
        "confidence": 0.87,
        "last_updated": "2025-06-15T10:30:00Z"
    }
}
```

---

## Community & Rankings Endpoints

### Community Rankings

#### GET /api/community-rankings/clubs/{platform?}
**Purpose:** Get club leaderboards

**Query Parameters:**
```
?period=weekly,monthly,seasonal
?period_number=integer
?limit=integer (max 100)
?division=string
```

**Response (200 OK):**
```json
{
    "success": true,
    "data": {
        "period": {
            "type": "weekly",
            "number": 25,
            "start_date": "2025-06-09",
            "end_date": "2025-06-15"
        },
        "rankings": [
            {
                "position": 1,
                "previous_position": 2,
                "club": {
                    "id": 1,
                    "name": "Manchester United FC",
                    "platform": "xbox",
                    "badge_id": "man_utd_001"
                },
                "skill_rating": 2450,
                "vp_points": 1850,
                "games_played": 12,
                "wins": 10,
                "losses": 2,
                "draws": 0,
                "win_percentage": 83.33
            }
        ],
        "meta": {
            "total_clubs": 150,
            "platform": "xbox"
        }
    }
}
```

#### GET /api/community-rankings/players/{platform?}
**Purpose:** Get player leaderboards

**Query Parameters:**
```
?period=weekly,monthly,seasonal
?period_number=integer
?position=GK,DEF,MID,ATT
?club_id=integer
?limit=integer (max 100)
?sort=vpr_rating,goals,assists,overall_rating
```

**Response (200 OK):**
```json
{
    "success": true,
    "data": {
        "period": {...},
        "rankings": [
            {
                "overall_rank": 1,
                "position_rank": 1,
                "player": {
                    "id": 1,
                    "name": "Cristiano Ronaldo",
                    "position": "ST",
                    "overall_rating": 95,
                    "club": {...}
                },
                "vpr_rating": 9.2,
                "appearances": 12,
                "goals": 18,
                "assists": 6,
                "average_rating": 8.4,
                "player_of_match": 8
            }
        ],
        "meta": {...}
    }
}
```

### User Favorites

#### GET /api/user-favourite-clubs
**Purpose:** Get user's favorite clubs
**Authentication:** Required

**Response (200 OK):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "club": {
                "id": 1,
                "name": "Manchester United FC",
                "platform": "xbox",
                "skill_rating": 2450,
                "badge_id": "man_utd_001"
            },
            "order_index": 0,
            "created_at": "2025-06-01T10:00:00Z"
        }
    ]
}
```

#### POST /api/user-favourite-clubs
**Purpose:** Add club to favorites
**Authentication:** Required

**Request:**
```json
{
    "club_id": "integer|required|exists:clubs,id",
    "order_index": "integer|optional|min:0"
}
```

#### DELETE /api/user-favourite-clubs/{id}
**Purpose:** Remove club from favorites
**Authentication:** Required

---

## EA Sports API Proxy Endpoints

### Club Information

#### GET /api/ea/clubs/{platform}/{eaClubId}
**Purpose:** Get club information from EA API
**Rate Limit:** 10 requests/minute

**Response (200 OK):**
```json
{
    "success": true,
    "data": {
        "ea_club_id": "123456",
        "name": "Manchester United FC",
        "platform": "xbox",
        "reputation": "world_renowned",
        "skill_rating": 2450,
        "recent_results": [...],
        "club_info": {...},
        "cached": true,
        "cache_expires_at": "2025-06-15T11:30:00Z"
    }
}
```

#### GET /api/ea/matches/{matchType}/{platform}/{eaClubId}
**Purpose:** Get match history from EA API

**Path Parameters:**
- `matchType`: league, playoff, friendly, rush
- `platform`: xbox, playstation, pc
- `eaClubId`: EA Club ID

**Query Parameters:**
```
?limit=integer (max 50)
?offset=integer
```

#### GET /api/ea/members/{platform}/{eaClubId}
**Purpose:** Get club member statistics from EA API

#### GET /api/ea/search/{platform}/{clubName}
**Purpose:** Search for clubs by name via EA API

**Query Parameters:**
```
?exact=boolean (exact match)
?limit=integer (max 20)
```

### Club Comparison

#### GET /api/ea/clubs/compare-clubs-info/{platform}/{eaClubId1}/{eaClubId2}
**Purpose:** Compare two clubs' basic information

**Response (200 OK):**
```json
{
    "success": true,
    "data": {
        "club1": {...},
        "club2": {...},
        "comparison": {
            "skill_rating_difference": 150,
            "reputation_comparison": "club1_higher",
            "win_rate_difference": 5.2
        }
    }
}
```

#### GET /api/ea/clubs/compare-career-stats/{platform}/{eaClubId1}/{eaClubId2}
**Purpose:** Compare career statistics between two clubs

#### GET /api/ea/clubs/compare-member-stats/{platform}/{eaClubId1}/{eaClubId2}
**Purpose:** Compare member statistics between two clubs

---

## Error Handling

### HTTP Status Codes
- **200 OK** - Request successful
- **201 Created** - Resource created successfully
- **204 No Content** - Successful request with no response body
- **400 Bad Request** - Invalid request parameters
- **401 Unauthorized** - Authentication required
- **403 Forbidden** - Insufficient permissions
- **404 Not Found** - Resource not found
- **422 Unprocessable Entity** - Validation errors
- **429 Too Many Requests** - Rate limit exceeded
- **500 Internal Server Error** - Server error

### Error Response Format
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "email": ["The email field is required."],
        "password": ["The password must be at least 8 characters."]
    },
    "meta": {
        "error_code": "VALIDATION_FAILED",
        "timestamp": "2025-06-15T10:30:00Z",
        "request_id": "req_abc123",
        "documentation_url": "https://docs.example.com/errors/validation-failed"
    }
}
```

### Common Error Codes
- `VALIDATION_FAILED` - Request validation errors
- `AUTHENTICATION_REQUIRED` - Authentication token missing
- `INVALID_TOKEN` - Authentication token invalid/expired
- `INSUFFICIENT_PERMISSIONS` - User lacks required permissions
- `RESOURCE_NOT_FOUND` - Requested resource doesn't exist
- `RATE_LIMIT_EXCEEDED` - API rate limit exceeded
- `EA_API_ERROR` - EA Sports API error
- `AI_ANALYSIS_FAILED` - Prism AI analysis error
- `FILE_UPLOAD_ERROR` - File upload/processing error

---

## Rate Limiting

### Rate Limit Headers
All responses include rate limiting headers:
```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 45
X-RateLimit-Reset: 1623456789
X-RateLimit-Retry-After: 30
```

### Rate Limit Tiers
1. **General API**: 60 requests/minute
2. **EA Proxy Endpoints**: 10 requests/minute
3. **AI Analysis**: 5 requests/minute
4. **File Uploads**: 3 requests/minute
5. **Authentication**: 5 attempts/minute

---

## Authentication & Authorization

### Token-Based Authentication
```http
Authorization: Bearer {token}
```

### Permission System
- **Guest**: Public endpoints only
- **User**: Basic CRUD operations
- **Club Member**: Club-specific operations
- **Club Admin**: Club management operations
- **Club Owner**: Full club control
- **System Admin**: Full system access

### Protected Endpoints
Most endpoints requiring authentication will return:
```json
{
    "success": false,
    "message": "Unauthenticated",
    "meta": {
        "error_code": "AUTHENTICATION_REQUIRED"
    }
}
```

---

## Caching Strategy

### Cache Headers
```
Cache-Control: public, max-age=300
ETag: "abc123def456"
Last-Modified: Wed, 15 Jun 2025 10:30:00 GMT
```

### Cached Endpoints
- **EA API data**: 5 minutes
- **Club rankings**: 15 minutes
- **Player statistics**: 10 minutes
- **AI analysis results**: 1 hour
- **Static data**: 24 hours

---

## Versioning Strategy

### API Versioning
- **Current Version**: v1
- **URL Pattern**: `/api/v1/...`
- **Header Support**: `Accept: application/vnd.proclubs.v1+json`

### Deprecation Policy
- **Notice Period**: 6 months
- **Support Period**: 12 months
- **Migration Guide**: Provided for breaking changes

---

## SDK & Integration

### Frontend Integration (React/TypeScript)
```typescript
// API Client Configuration
import axios from 'axios';

const apiClient = axios.create({
    baseURL: '/api/v1',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
});

// Authentication Interceptor
apiClient.interceptors.request.use((config) => {
    const token = localStorage.getItem('auth_token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

// Response Interceptor
apiClient.interceptors.response.use(
    (response) => response.data,
    (error) => {
        if (error.response?.status === 401) {
            // Handle authentication error
            localStorage.removeItem('auth_token');
            window.location.href = '/login';
        }
        return Promise.reject(error.response?.data || error);
    }
);

// Example Usage
interface Club {
    id: number;
    name: string;
    platform: string;
    skill_rating: number;
}

interface ApiResponse<T> {
    success: boolean;
    data: T;
    meta?: any;
}

const getClubs = async (params?: any): Promise<ApiResponse<Club[]>> => {
    return apiClient.get('/clubs', { params });
};
```

### Type Definitions
```typescript
// Common Types
export type Platform = 'xbox' | 'playstation' | 'pc';
export type Position = 'GK' | 'RB' | 'CB' | 'LB' | 'CDM' | 'CM' | 'CAM' | 'RM' | 'LM' | 'RW' | 'LW' | 'CF' | 'ST';
export type MatchType = 'league' | 'playoff' | 'friendly' | 'rush';
export type Outcome = 'home_win' | 'away_win' | 'draw';

// API Response Types
export interface PaginatedResponse<T> {
    success: boolean;
    data: T[];
    meta: {
        current_page: number;
        from: number;
        last_page: number;
        per_page: number;
        to: number;
        total: number;
    };
    links: {
        first: string;
        last: string;
        prev: string | null;
        next: string | null;
    };
}

// Resource Types
export interface Club {
    id: number;
    name: string;
    ea_club_id?: string;
    platform: Platform;
    skill_rating: number;
    reputation: string;
    badge_id?: string;
    player_count?: number;
    is_active: boolean;
    created_at: string;
    updated_at: string;
}

export interface Player {
    id: number;
    name: string;
    ea_player_id?: string;
    platform: Platform;
    club?: Club;
    ea_pro_position?: Position;
    ea_pro_overall?: number;
    ea_pro_height?: string;
    ea_pro_nationality?: string;
    vpr_rating: number;
    career_stats: {
        appearances: number;
        goals: number;
        assists: number;
        clean_sheets: number;
        player_of_match: number;
        red_cards: number;
        pass_completion_rate: number;
        shots_on_target_rate: number;
    };
    performance_trend: 'improving' | 'declining' | 'stable';
    last_played_at?: string;
    created_at: string;
    updated_at: string;
}
```

---

## Testing & Documentation

### API Testing
- **Unit Tests**: PHPUnit for Laravel backend
- **Integration Tests**: Feature tests for complete API flows
- **Load Testing**: Artillery.js for performance testing
- **Security Testing**: OWASP compliance checks

### Interactive Documentation
- **Swagger/OpenAPI**: Complete API specification
- **Postman Collection**: Pre-configured API requests
- **Code Examples**: Multiple language implementations

### Monitoring
- **Laravel Telescope**: Request debugging and profiling
- **Laravel Pulse**: Performance monitoring
- **Error Tracking**: Structured error logging
- **Analytics**: API usage statistics

---

This comprehensive API design provides a robust foundation for the Pro Clubs Companion v3 application, supporting all planned features including AI analytics, community rankings, real-time EA Sports integration, and advanced player statistics management.