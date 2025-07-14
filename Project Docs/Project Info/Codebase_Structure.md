# Codebase Structure - Pro Clubs

## Overview

Pro Clubs is a comprehensive Laravel 12 application with React/TypeScript frontend using Inertia.js. The codebase follows modern Laravel conventions with additional structure for complex domain logic, AI integration, and community features.

## Technology Stack
- **Backend**: Laravel 12, PHP 8.2+, MySQL, Redis
- **Frontend**: React 19, TypeScript, Inertia.js, TailwindCSS 4.0, Vite 6.0
- **UI Framework**: Radix UI + shadcn/ui components
- **Development Tools**: Laravel Telescope, Horizon, Pulse, Pail

## Root Directory Structure

```
pro-clubs-v3/
├── app/                    # Laravel application code
├── bootstrap/              # Application bootstrap files
├── config/                 # Configuration files
├── database/               # Database migrations, seeds, factories
├── Project Docs/           # Project documentation
├── public/                 # Web server document root
├── resources/              # Views, frontend assets, language files
├── routes/                 # Application routes
├── storage/                # Generated files, logs, cache
├── tests/                  # Test files
├── vendor/                 # Composer dependencies
├── .env.example            # Environment configuration template
├── composer.json           # PHP dependencies
├── package.json            # Node.js dependencies
├── vite.config.js          # Vite build configuration
└── tailwind.config.js      # Tailwind CSS configuration
```

## Backend Structure (Laravel)

### App Directory (`/app`)

```
app/
├── Console/                # Artisan commands
├── DTOs/                   # Data Transfer Objects
├── Enums/                  # PHP Enumerations
├── Exceptions/             # Custom exception classes
├── Http/                   # HTTP layer
│   ├── Controllers/        # Route controllers
│   ├── Middleware/         # HTTP middleware
│   ├── Requests/           # Form request validation
│   └── Resources/          # API resources
├── Jobs/                   # Queued jobs
├── Models/                 # Eloquent models
├── Observers/              # Model observers
├── Policies/               # Authorization policies
├── Providers/              # Service providers
├── Services/               # Business logic services
└── Traits/                 # Reusable traits
```

#### Controllers (`/app/Http/Controllers`)
```
Controllers/
├── Auth/                   # Authentication controllers
├── Admin/                  # Admin panel controllers
├── Api/                    # API controllers
├── Community/              # Community ranking controllers
├── Player/                 # Player management controllers
├── Club/                   # Club management controllers
├── Result/                 # Match result controllers
├── UserController.php      # User management
└── Controller.php          # Base controller
```

#### Models (`/app/Models`)
```
Models/
├── User.php               # User model
├── Player.php             # Player model
├── Club.php               # Club model
├── Result.php             # Match result model
├── PlayerAttribute.php    # Player attributes
├── JsonResultArchive.php  # Match result archive
├── UserFavouriteClub.php  # User-club relationships
└── CommunityRanking.php   # Community rankings
```

#### Services (`/app/Services`)
```
Services/
├── AbstractService.php            # Base service class
├── PrismService.php              # AI/ML integration service
├── ImageAnalysisService.php      # Image processing
├── PrismCacheService.php         # AI response caching
├── EA/                           # EA Sports API integration
│   └── ClubsApiService.php
└── ProClubs/                     # Domain-specific services
    ├── ClubService.php
    ├── PlayerService.php
    ├── ResultService.php
    └── CommunityRankingService.php
```

#### Data Transfer Objects (`/app/DTOs`)
```
DTOs/
├── Player/                    # Player-related DTOs
├── Club/                      # Club-related DTOs
├── Result/                    # Match result DTOs
└── Community/                 # Community ranking DTOs
```

#### Enums (`/app/Enums`)
```
Enums/
├── Platform.php              # Gaming platforms
├── Position.php              # Player positions
├── MatchType.php             # Match types
└── RankingType.php           # Ranking categories
```

### Database Structure (`/database`)

```
database/
├── factories/              # Model factories for testing
├── migrations/             # Database migrations
├── seeders/               # Database seeders
└── database.sqlite        # Default SQLite database
```

#### Key Migrations
- `create_users_table` - User authentication
- `create_players_table` - Player data
- `create_clubs_table` - Club information
- `create_results_table` - Match results
- `create_player_attributes_table` - Player statistics
- `create_community_rankings_table` - Community rankings

### Routes (`/routes`)

```
routes/
├── web.php                # Web routes (Inertia.js)
├── api.php                # API routes
├── console.php            # Artisan commands
└── channels.php           # Broadcasting channels
```

### Configuration (`/config`)

```
config/
├── app.php                # Application configuration
├── database.php           # Database connections
├── cache.php              # Cache configuration
├── queue.php              # Queue configuration
├── mail.php               # Mail configuration
├── filesystems.php        # File storage
├── services.php           # Third-party services
└── inertia.php            # Inertia.js configuration
```

## Frontend Structure (React/TypeScript)

### Resources Directory (`/resources`)

```
resources/
├── css/                   # Stylesheets
│   └── app.css           # Main stylesheet
├── js/                    # JavaScript/TypeScript code
│   ├── components/        # Reusable components
│   ├── hooks/            # Custom React hooks
│   ├── layouts/          # Layout components
│   ├── pages/            # Page components (Inertia.js)
│   ├── types/            # TypeScript type definitions
│   ├── utils/            # Utility functions
│   ├── app.tsx           # Application entry point
│   └── bootstrap.ts      # Application bootstrap
└── views/                # Blade templates (minimal with Inertia.js)
```

### Components (`/resources/js/components`)

```
components/
├── ui/                    # Base UI components (shadcn/ui)
│   ├── button.tsx
│   ├── input.tsx
│   ├── card.tsx
│   ├── dialog.tsx
│   └── ...
├── forms/                 # Form components
├── navigation/            # Navigation components
├── tables/                # Data table components
├── charts/                # Chart components
└── community/             # Community-specific components
```

### Pages (`/resources/js/pages`)

```
pages/
├── Auth/                  # Authentication pages
│   ├── Login.tsx
│   ├── Register.tsx
│   └── ForgotPassword.tsx
├── Dashboard/             # Dashboard pages
├── Players/               # Player management pages
│   ├── Index.tsx
│   ├── Show.tsx
│   └── Create.tsx
├── Clubs/                 # Club management pages
├── Results/               # Match result pages
├── Community/             # Community ranking pages
└── Profile/               # User profile pages
```

### Layouts (`/resources/js/layouts`)

```
layouts/
├── AppLayout.tsx          # Main application layout
├── AuthLayout.tsx         # Authentication layout
├── GuestLayout.tsx        # Guest user layout
└── DashboardLayout.tsx    # Dashboard layout
```

### Types (`/resources/js/types`)

```
types/
├── index.ts              # Global type definitions
├── api.ts                # API response types
├── models.ts             # Model types
├── forms.ts              # Form types
└── community.ts          # Community feature types
```

### Hooks (`/resources/js/hooks`)

```
hooks/
├── useAuth.ts            # Authentication hook
├── useApi.ts             # API request hook
├── useLocalStorage.ts    # Local storage hook
└── useCommunityData.ts   # Community data hook
```

## Asset Organization

### Stylesheets
- **TailwindCSS**: Utility-first CSS framework
- **Custom CSS**: Application-specific styles in `resources/css/app.css`
- **Component Styles**: Co-located with components when needed

### JavaScript/TypeScript
- **Vite**: Modern build tool with hot module replacement
- **TypeScript**: Full type safety throughout the application
- **React 19**: Latest React features including concurrent rendering

### Build Configuration
- **Vite**: `vite.config.js` - Build tool configuration
- **Tailwind**: `tailwind.config.js` - CSS framework configuration
- **TypeScript**: `tsconfig.json` - TypeScript compiler options

## Testing Structure (`/tests`)

```
tests/
├── Feature/               # Feature tests (HTTP testing)
│   ├── Auth/             # Authentication tests
│   ├── Players/          # Player management tests
│   ├── Clubs/            # Club management tests
│   └── API/              # API endpoint tests
├── Unit/                  # Unit tests
│   ├── Services/         # Service layer tests
│   ├── Models/           # Model tests
│   └── Helpers/          # Helper function tests
├── CreatesApplication.php # Test application setup
└── TestCase.php          # Base test case
```

## Storage Organization (`/storage`)

```
storage/
├── app/                   # Application files
│   ├── public/           # Publicly accessible files
│   └── private/          # Private files
├── framework/             # Framework-generated files
│   ├── cache/            # Application cache
│   ├── sessions/         # Session files
│   └── views/            # Compiled views
└── logs/                 # Application logs
```

## Public Assets (`/public`)

```
public/
├── build/                # Compiled assets (generated by Vite)
├── storage/              # Symbolic link to storage/app/public
├── favicon.ico           # Site favicon
├── robots.txt            # Search engine directives
└── index.php             # Application entry point
```

## Development Workflow Files

### Package Management
- **composer.json**: PHP dependencies and scripts
- **composer.lock**: Locked PHP dependency versions
- **package.json**: Node.js dependencies and scripts
- **package-lock.json**: Locked Node.js dependency versions

### Environment Configuration
- **.env.example**: Environment variable template
- **.env**: Environment configuration (not in version control)

### Version Control
- **.gitignore**: Git ignore patterns
- **.gitattributes**: Git attributes configuration

### Code Quality
- **phpunit.xml**: PHPUnit testing configuration
- **pint.json**: Laravel Pint code style configuration

## Key Features Organization

### AI/ML Integration (Prism)
```
app/Services/PrismService.php              # AI service integration
app/Services/ImageAnalysisService.php      # Image processing
app/Services/PrismCacheService.php         # AI response caching
resources/js/pages/prism/                  # AI-related frontend pages
```

### Community Rankings
```
app/Models/CommunityRanking.php            # Rankings model
app/Services/ProClubs/CommunityRankingService.php  # Rankings logic
app/Http/Controllers/Community/            # Ranking controllers
resources/js/pages/Community/              # Community frontend
```

### Player Management
```
app/Models/Player.php                      # Player model
app/Models/PlayerAttribute.php             # Player attributes
app/Services/ProClubs/PlayerService.php    # Player business logic
resources/js/pages/Players/                # Player management UI
```

### Match Results
```
app/Models/Result.php                      # Match results model
app/Models/JsonResultArchive.php           # Archived results
app/Services/ProClubs/ResultService.php    # Result processing
resources/js/pages/Results/                # Results UI
```

## Development Tools Integration

### Laravel Telescope
- **URL**: `/telescope`
- **Purpose**: Debugging and monitoring
- **Configuration**: `config/telescope.php`

### Laravel Horizon
- **URL**: `/horizon`
- **Purpose**: Queue monitoring
- **Configuration**: `config/horizon.php`

### Laravel Pulse
- **URL**: `/pulse`
- **Purpose**: Application monitoring
- **Configuration**: `config/pulse.php`

## API Organization

### RESTful API Routes
- **Base URL**: `/api/`
- **Authentication**: Laravel Sanctum
- **Controllers**: `app/Http/Controllers/Api/`
- **Resources**: `app/Http/Resources/`

### API Structure
```
api/
├── auth/                  # Authentication endpoints
├── users/                 # User management
├── players/               # Player CRUD
├── clubs/                 # Club CRUD
├── results/               # Match results
├── community-rankings/    # Community features
└── ea/                    # EA Sports API proxy
```

## Deployment Structure

### Production Optimization
- **Asset Compilation**: `npm run build`
- **Composer Optimization**: `composer install --optimize-autoloader --no-dev`
- **Laravel Optimization**: Configuration, route, and view caching

### Server Requirements
- **PHP**: 8.2+ with required extensions
- **Database**: MySQL 8.0+ or PostgreSQL
- **Cache**: Redis (recommended)
- **Queue**: Redis or database
- **Web Server**: Nginx or Apache

This structure provides a solid foundation for a scalable, maintainable FIFA/EA Sports FC community management platform with modern development practices and comprehensive feature set.