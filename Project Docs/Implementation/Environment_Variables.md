# Environment Variables - Pro Clubs

## Overview

This document outlines all environment variables required for Pro Clubs application. These variables configure the application's behavior, external service integrations, and deployment settings.

## Core Application Settings

### Application Configuration
```env
APP_NAME="Pro Clubs"                    # Application name displayed in UI
APP_ENV=local                      # Environment (local, staging, production)
APP_KEY=                          # Laravel encryption key (generated via php artisan key:generate)
APP_DEBUG=true                    # Enable debug mode (false in production)
APP_URL=http://localhost          # Base application URL
```

### Localization
```env
APP_LOCALE=en                     # Default application locale
APP_FALLBACK_LOCALE=en           # Fallback locale when translation missing
APP_FAKER_LOCALE=en_US           # Locale for Faker library (testing/seeding)
```

### Performance & Maintenance
```env
APP_MAINTENANCE_DRIVER=file      # Maintenance mode storage driver
# APP_MAINTENANCE_STORE=database  # Alternative: database storage for maintenance mode
PHP_CLI_SERVER_WORKERS=4         # Number of PHP CLI server workers
BCRYPT_ROUNDS=12                 # BCrypt hashing rounds (higher = more secure, slower)
```

## Database Configuration

### Primary Database
```env
DB_CONNECTION=sqlite             # Database driver (sqlite, mysql, pgsql)
# DB_HOST=127.0.0.1             # Database host (for MySQL/PostgreSQL)
# DB_PORT=3306                  # Database port
# DB_DATABASE=laravel           # Database name
# DB_USERNAME=root              # Database username
# DB_PASSWORD=                  # Database password
```

**Production Example (MySQL):**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pro_clubs_v3
DB_USERNAME=db_user
DB_PASSWORD=secure_password
```

## Session & Cache Configuration

### Session Management
```env
SESSION_DRIVER=database          # Session storage (database, redis, file)
SESSION_LIFETIME=120             # Session lifetime in minutes
SESSION_ENCRYPT=false            # Encrypt session data
SESSION_PATH=/                   # Session cookie path
SESSION_DOMAIN=null              # Session cookie domain
```

### Caching
```env
CACHE_STORE=database             # Cache driver (database, redis, file, memcached)
# CACHE_PREFIX=                  # Cache key prefix (optional)
```

**Production Recommendation (Redis):**
```env
SESSION_DRIVER=redis
CACHE_STORE=redis
```

## Queue & Broadcasting

### Queue Configuration
```env
QUEUE_CONNECTION=database        # Queue driver (database, redis, sync)
```

### Broadcasting
```env
BROADCAST_CONNECTION=log         # Broadcasting driver (log, redis, pusher)
```

**Production Recommendation:**
```env
QUEUE_CONNECTION=redis
BROADCAST_CONNECTION=redis
```

## Redis Configuration

### Redis Settings
```env
REDIS_CLIENT=phpredis            # Redis client (phpredis, predis)
REDIS_HOST=127.0.0.1            # Redis server host
REDIS_PASSWORD=null             # Redis password (if required)
REDIS_PORT=6379                 # Redis port
```

### Memcached (Alternative)
```env
MEMCACHED_HOST=127.0.0.1        # Memcached server host
```

## File Storage

### Local/Cloud Storage
```env
FILESYSTEM_DISK=local           # Default filesystem disk
```

### AWS S3 Configuration
```env
AWS_ACCESS_KEY_ID=              # AWS access key
AWS_SECRET_ACCESS_KEY=          # AWS secret key
AWS_DEFAULT_REGION=us-east-1    # AWS region
AWS_BUCKET=                     # S3 bucket name
AWS_USE_PATH_STYLE_ENDPOINT=false # Path-style endpoint (for MinIO/compatible)
```

**Production S3 Example:**
```env
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=AKIA...
AWS_SECRET_ACCESS_KEY=secret...
AWS_DEFAULT_REGION=us-west-2
AWS_BUCKET=pro-clubs-v3-storage
```

## Email Configuration

### Mail Settings
```env
MAIL_MAILER=log                 # Mail driver (log, smtp, ses, mailgun)
MAIL_SCHEME=null               # Mail scheme
MAIL_HOST=127.0.0.1            # SMTP host
MAIL_PORT=2525                 # SMTP port
MAIL_USERNAME=null             # SMTP username
MAIL_PASSWORD=null             # SMTP password
MAIL_FROM_ADDRESS="hello@example.com"  # Default from address
MAIL_FROM_NAME="${APP_NAME}"   # Default from name
```

**Production SMTP Example:**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=587
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS="noreply@proclubsv3.com"
MAIL_FROM_NAME="Pro Clubs"
```

## Logging Configuration

### Log Settings
```env
LOG_CHANNEL=stack               # Log channel (stack, single, daily, slack)
LOG_STACK=single               # Stack channel configuration
LOG_DEPRECATIONS_CHANNEL=null  # Deprecation warnings channel
LOG_LEVEL=debug                # Log level (debug, info, notice, warning, error)
```

**Production Recommendation:**
```env
LOG_CHANNEL=daily
LOG_LEVEL=warning
```

## Frontend Configuration

### Vite Build Tool
```env
VITE_APP_NAME="${APP_NAME}"     # App name available to frontend
```

**Additional Frontend Variables:**
```env
VITE_API_URL="${APP_URL}/api"   # API base URL for frontend
VITE_PUSHER_APP_KEY=            # Pusher key for real-time features
```

## Development Tools

### Laravel Telescope
```env
TELESCOPE_ALLOWED_EMAILS=""     # Comma-separated emails allowed to access Telescope
```

**Production Example:**
```env
TELESCOPE_ALLOWED_EMAILS="admin@company.com,dev@company.com"
```

## AI/ML Integration (Prism)

### AI Service Providers
```env
GEMINI_API_KEY=                 # Google Gemini API key
GEMINI_URL=                     # Custom Gemini API URL (optional)
CLAUDE_API_KEY=                 # Anthropic Claude API key
```

**Additional AI Providers:**
```env
OPENAI_API_KEY=                 # OpenAI API key
OLLAMA_URL=http://localhost:11434  # Ollama local installation URL
```

## OAuth & Social Authentication

### Google OAuth
```env
GOOGLE_CLIENT_ID=               # Google OAuth client ID
GOOGLE_CLIENT_SECRET=           # Google OAuth client secret
GOOGLE_REDIRECT=/auth/google/callback  # OAuth callback URL
```

### GitHub OAuth
```env
GITHUB_CLIENT_ID=               # GitHub OAuth client ID
GITHUB_CLIENT_SECRET=           # GitHub OAuth client secret
GITHUB_REDIRECT_URI=/auth/github/callback  # OAuth callback URL
```

## Environment-Specific Configurations

### Local Development
```env
APP_ENV=local
APP_DEBUG=true
DB_CONNECTION=sqlite
CACHE_STORE=database
QUEUE_CONNECTION=sync
MAIL_MAILER=log
```

### Staging Environment
```env
APP_ENV=staging
APP_DEBUG=true
DB_CONNECTION=mysql
CACHE_STORE=redis
QUEUE_CONNECTION=redis
MAIL_MAILER=smtp
LOG_LEVEL=info
```

### Production Environment
```env
APP_ENV=production
APP_DEBUG=false
DB_CONNECTION=mysql
CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
FILESYSTEM_DISK=s3
MAIL_MAILER=smtp
LOG_LEVEL=error
LOG_CHANNEL=daily
```

## Security Considerations

### Sensitive Variables
**Never commit these to version control:**
- `APP_KEY`
- `DB_PASSWORD`
- `REDIS_PASSWORD`
- `AWS_SECRET_ACCESS_KEY`
- `MAIL_PASSWORD`
- `*_API_KEY` variables
- `*_CLIENT_SECRET` variables

### Environment Security
1. **Use `.env` files** for environment-specific configuration
2. **Rotate keys regularly** especially API keys and database passwords
3. **Use different keys** for each environment
4. **Implement secret management** in production (AWS Secrets Manager, etc.)
5. **Restrict access** to environment files

## Required vs Optional Variables

### Required for Basic Functionality
- `APP_KEY` (generate with `php artisan key:generate`)
- `APP_URL`
- Database configuration (DB_*)
- `MAIL_FROM_ADDRESS`

### Required for Full Functionality
- `GEMINI_API_KEY` or `CLAUDE_API_KEY` (AI features)
- OAuth credentials (social login)
- AWS S3 credentials (file storage)
- Redis configuration (production)

### Optional Enhancements
- `TELESCOPE_ALLOWED_EMAILS` (development debugging)
- Broadcasting configuration (real-time features)
- Additional mail providers

## Environment Setup Commands

### Initial Setup
```bash
# Copy example environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database and run migrations
php artisan migrate

# Clear and cache configuration
php artisan config:clear
php artisan config:cache
```

### Production Optimization
```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev
```

## Troubleshooting

### Common Issues
1. **APP_KEY not set**: Run `php artisan key:generate`
2. **Database connection failed**: Verify DB_* credentials
3. **Redis connection failed**: Check Redis server status
4. **Mail not sending**: Verify MAIL_* configuration
5. **Assets not loading**: Check APP_URL and Vite configuration

### Environment Validation
```bash
# Test database connection
php artisan migrate:status

# Test cache configuration
php artisan cache:clear

# Test queue configuration
php artisan queue:work --once

# Validate environment
php artisan about
```