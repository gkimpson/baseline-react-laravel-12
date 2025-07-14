# Setup Guides - Pro Clubs

## Table of Contents
1. [System Requirements](#system-requirements)
2. [Quick Start](#quick-start)
3. [Installation Steps](#installation-steps)
4. [Development Environment](#development-environment)
5. [Database Setup](#database-setup)
6. [Frontend Setup](#frontend-setup)
7. [Configuration](#configuration)
8. [Service Dependencies](#service-dependencies)
9. [Development Tools](#development-tools)
10. [Testing Setup](#testing-setup)
11. [Production Deployment](#production-deployment)
12. [Docker Setup](#docker-setup)
13. [Troubleshooting](#troubleshooting)

## System Requirements

### Backend Requirements
- **PHP**: 8.2 or higher
- **Composer**: 2.0 or higher
- **Database**: SQLite (default) or MySQL 8.0+
- **Web Server**: Apache/Nginx (production)

### Frontend Requirements
- **Node.js**: 18.0 or higher
- **npm**: 9.0 or higher (or Yarn 1.22+)

### Optional Services
- **Redis**: 7.0+ (recommended for production)
- **Memcached**: Alternative caching solution
- **Docker**: For containerized development

### Development Tools
- **Git**: Version control
- **Code Editor**: VS Code, PhpStorm, or similar

## Quick Start

For the fastest setup using default SQLite database:

```bash
# Clone repository
git clone [repository-url] pro-clubs-v3
cd pro-clubs-v3

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Setup database
php artisan migrate --seed

# Start development servers
composer run dev
```

The application will be available at `http://localhost:8000`.

## Installation Steps

### 1. Clone Repository
```bash
git clone [repository-url] pro-clubs-v3
cd pro-clubs-v3
```

### 2. Install PHP Dependencies
```bash
composer install
```

### 3. Install Node.js Dependencies
```bash
npm install
# or
yarn install
```

### 4. Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 5. Database Setup
```bash
# Create database (for SQLite, this is automatic)
touch database/database.sqlite

# Run migrations
php artisan migrate

# Seed database (optional)
php artisan db:seed
```

### 6. Build Frontend Assets
```bash
# Development build
npm run dev

# Production build
npm run build
```

### 7. Start Development Server
```bash
# Laravel development server
php artisan serve

# Or use the concurrent development command
composer run dev
```

## Development Environment

### Option 1: Manual Setup

#### Backend Setup
```bash
# Install Composer dependencies
composer install

# Setup Laravel
cp .env.example .env
php artisan key:generate
php artisan migrate --seed

# Start PHP server
php artisan serve
```

#### Frontend Setup
```bash
# Install Node dependencies
npm install

# Start Vite development server
npm run dev
```

### Option 2: Concurrent Development
The project includes a convenient script to run both backend and frontend:

```bash
composer run dev
```

This command starts:
- Laravel development server (`php artisan serve`)
- Vite development server (`npm run dev`)

### Option 3: Laravel Sail (Docker)
```bash
# Install Sail
composer require laravel/sail --dev

# Setup Sail
php artisan sail:install

# Start containers
./vendor/bin/sail up -d

# Run commands via Sail
./vendor/bin/sail artisan migrate
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
```

## Database Setup

### SQLite (Default - Recommended for Development)
SQLite is pre-configured and requires no additional setup:

```bash
# Database file is automatically created
php artisan migrate --seed
```

### MySQL Setup
For production or if you prefer MySQL:

1. **Update `.env` file:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pro_clubs_v3
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

2. **Create database:**
```sql
CREATE DATABASE pro_clubs_v3 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

3. **Run migrations:**
```bash
php artisan migrate --seed
```

### Database Commands
```bash
# Check migration status
php artisan migrate:status

# Rollback migrations
php artisan migrate:rollback

# Fresh migration with seeding
php artisan migrate:fresh --seed

# Generate test data
php artisan db:seed
```

## Frontend Setup

### React + TypeScript Development
The frontend uses React 19 with TypeScript and Vite:

```bash
# Install dependencies
npm install

# Development server with hot reload
npm run dev

# Type checking
npm run type-check

# Build for production
npm run build

# Preview production build
npm run preview
```

### Frontend Structure
```
resources/js/
├── components/         # Reusable UI components
├── pages/             # Inertia.js page components
├── layouts/           # Layout components
├── types/             # TypeScript type definitions
├── utils/             # Utility functions
└── app.tsx           # Main application entry
```

### Styling
- **TailwindCSS 4.0** for utility-first styling
- **Radix UI** for accessible component primitives
- **Custom CSS** in `resources/css/app.css`

## Configuration

### Essential Environment Variables
```env
# Application
APP_NAME="Pro Clubs V3"
APP_ENV=local
APP_KEY=base64:... # Generated by php artisan key:generate
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database (SQLite default)
DB_CONNECTION=sqlite

# Mail (development)
MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@proclubsv3.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### Optional Configuration
```env
# AI Integration
GEMINI_API_KEY=your_gemini_key
CLAUDE_API_KEY=your_claude_key

# Social Authentication
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_secret
GITHUB_CLIENT_ID=your_github_client_id
GITHUB_CLIENT_SECRET=your_github_secret

# Redis (production)
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# AWS S3 (file storage)
AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your_bucket_name
```

## Service Dependencies

### Redis Setup (Production Recommended)
```bash
# Install Redis (Ubuntu/Debian)
sudo apt update
sudo apt install redis-server

# Install Redis (macOS with Homebrew)
brew install redis
brew services start redis

# Update .env for Redis
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### Queue Processing
```bash
# Start queue worker
php artisan queue:work

# Process specific queue
php artisan queue:work --queue=high,default

# Restart all workers
php artisan queue:restart
```

### Scheduled Tasks
Add to crontab for production:
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

## Development Tools

### Laravel Telescope (Debugging)
```bash
# Publish Telescope assets
php artisan telescope:install

# Access Telescope
# Visit: http://localhost:8000/telescope
```

### Laravel Horizon (Queue Monitoring)
```bash
# Start Horizon
php artisan horizon

# Access Horizon dashboard
# Visit: http://localhost:8000/horizon
```

### Laravel Pulse (Performance Monitoring)
```bash
# Install Pulse
php artisan pulse:install

# Access Pulse dashboard
# Visit: http://localhost:8000/pulse
```

### Laravel Pail (Log Monitoring)
```bash
# Monitor logs in real-time
php artisan pail

# Filter by log level
php artisan pail --filter="error"
```

### Development Commands
```bash
# Clear all caches
php artisan optimize:clear

# Cache configuration for performance
php artisan config:cache

# Generate IDE helper files
php artisan ide-helper:generate
```

## Testing Setup

### Backend Testing (Pest PHP)
```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage

# Run specific test
php artisan test --filter=UserTest

# Run tests with detailed output
php artisan test --verbose
```

### Frontend Testing
```bash
# Install testing dependencies (if needed)
npm install --save-dev @testing-library/react vitest

# Run tests
npm run test

# Run tests with coverage
npm run test:coverage
```

### Test Database
Tests use SQLite in-memory database by default. Configure in `phpunit.xml`:
```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

## Production Deployment

### Server Requirements
- **PHP**: 8.2+ with required extensions
- **Web Server**: Nginx or Apache
- **Database**: MySQL 8.0+
- **Redis**: For caching and queues
- **Node.js**: For asset compilation

### Optimization Commands
```bash
# Install production dependencies
composer install --optimize-autoloader --no-dev

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Build production assets
npm run build

# Generate optimized autoloader
composer dump-autoload --optimize
```

### Web Server Configuration

#### Nginx Configuration
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/pro-clubs-v3/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Process Management
```bash
# Use Supervisor for queue workers
sudo nano /etc/supervisor/conf.d/laravel-worker.conf

[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/pro-clubs-v3/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=8
redirect_stderr=true
stdout_logfile=/var/www/pro-clubs-v3/storage/logs/worker.log
```

## Docker Setup

### Manual Docker Setup
```dockerfile
# Dockerfile
FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Install Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
RUN apt-get install -y nodejs

COPY . .
RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build

EXPOSE 9000
CMD ["php-fpm"]
```

### Docker Compose
```yaml
version: '3.8'
services:
  app:
    build: .
    container_name: pro-clubs-v3
    volumes:
      - .:/var/www
    networks:
      - app-network

  webserver:
    image: nginx:alpine
    container_name: pro-clubs-nginx
    ports:
      - "80:80"
    volumes:
      - .:/var/www
      - ./docker/nginx:/etc/nginx/conf.d
    networks:
      - app-network

  db:
    image: mysql:8.0
    container_name: pro-clubs-db
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: pro_clubs_v3
    ports:
      - "3306:3306"
    networks:
      - app-network

  redis:
    image: redis:alpine
    container_name: pro-clubs-redis
    ports:
      - "6379:6379"
    networks:
      - app-network

networks:
  app-network:
    driver: bridge
```

### Laravel Sail
```bash
# Install Sail
composer require laravel/sail --dev

# Setup Sail environment
php artisan sail:install

# Start all services
./vendor/bin/sail up -d

# Access application at http://localhost
```

## Troubleshooting

### Common Issues

#### 1. Composer/PHP Issues
```bash
# Clear Composer cache
composer clear-cache

# Check PHP extensions
php -m

# Memory limit issues
php -d memory_limit=512M artisan migrate
```

#### 2. Database Issues
```bash
# Check database connection
php artisan tinker
>>> DB::connection()->getPdo();

# Reset database
php artisan migrate:fresh --seed

# Check SQLite permissions
ls -la database/database.sqlite
```

#### 3. Frontend Issues
```bash
# Clear Node modules
rm -rf node_modules package-lock.json
npm install

# Check Node version
node --version
npm --version

# Vite development server issues
npm run dev -- --host 0.0.0.0
```

#### 4. Permission Issues (Linux/Mac)
```bash
# Fix Laravel permissions
sudo chown -R $USER:www-data storage
sudo chown -R $USER:www-data bootstrap/cache
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

#### 5. Cache Issues
```bash
# Clear all caches
php artisan optimize:clear

# Individual cache clearing
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

### Debug Commands
```bash
# Check Laravel configuration
php artisan about

# View loaded configuration
php artisan config:show

# Test email configuration
php artisan tinker
>>> Mail::raw('Test', function($m) { $m->to('test@example.com')->subject('Test'); });

# Check queue status
php artisan queue:monitor

# View application logs
tail -f storage/logs/laravel.log
```

### Performance Issues
```bash
# Profile application
# Access /telescope for detailed profiling

# Check slow queries
# Enable query logging in config/database.php

# Monitor memory usage
php artisan tinker
>>> memory_get_usage(true);
```

### Getting Help
1. Check Laravel documentation: https://laravel.com/docs
2. Check Inertia.js documentation: https://inertiajs.com
3. Review application logs: `storage/logs/laravel.log`
4. Use Laravel Telescope for debugging: `/telescope`
5. Check GitHub issues and discussions

### Useful Development URLs
- **Application**: http://localhost:8000
- **Telescope**: http://localhost:8000/telescope
- **Horizon**: http://localhost:8000/horizon
- **Pulse**: http://localhost:8000/pulse
- **API Documentation**: http://localhost:8000/api (if configured)