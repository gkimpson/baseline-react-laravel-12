# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Development Commands

### PHP/Laravel Commands
- `composer dev` - Start development server with concurrent services (server, queue, logs, vite)
- `composer dev:ssr` - Start development with Server Side Rendering enabled
- `composer test` - Run PHP tests using Pest
- `php artisan serve` - Start Laravel development server only
- `php artisan test` - Run tests directly via artisan
- `php artisan migrate` - Run database migrations
- `php artisan tinker` - Laravel REPL for testing code

### JavaScript/React Commands
- `npm run dev` - Start Vite development server with hot reload
- `npm run build` - Build assets for production
- `npm run build:ssr` - Build with Server Side Rendering support
- `npm run lint` - Run ESLint with auto-fix
- `npm run format` - Format code with Prettier
- `npm run format:check` - Check code formatting
- `npm run types` - Run TypeScript type checking

### Testing
- Use `composer test` for PHP tests (Pest framework)
- Use `npm run types` for TypeScript type checking
- No dedicated JavaScript test runner configured currently

## Architecture Overview

### Full-Stack Structure
- **Backend**: Laravel 12 with Inertia.js server-side adapter
- **Frontend**: React 19 with TypeScript using Inertia.js client
- **Database**: SQLite (development), configured via Laravel migrations
- **Authentication**: Built-in Laravel auth with email verification

### Key Technologies
- **Laravel 12**: PHP framework handling API, auth, and server-side logic
- **Inertia.js 2**: Provides SPA-like experience without separate API
- **React 19**: Frontend framework with TypeScript support
- **Tailwind CSS 4**: Utility-first CSS framework
- **shadcn/ui**: React component library built on Radix UI primitives
- **Flowbite**: Tailwind CSS component library with pre-built components (Pro version available at https://flowbite.com/pro/)
- **Vite 6**: Frontend build tool with HMR support
- **TypeScript 5.7**: Strict type checking and modern JavaScript features
- **Pest 3.8**: Modern PHP testing framework

### Backend Package Ecosystem
**Laravel Extensions:**
- **Laravel Sanctum 4**: API authentication via tokens
- **Laravel Pulse 1.4**: Application monitoring and metrics
- **Laravel Reverb 1.0**: WebSocket server for real-time features
- **Laravel Socialite 5**: Social authentication (OAuth)
- **Laravel Tinker 2**: Interactive PHP REPL
- **Laravel Pail 1.2**: Log viewer and debugging

**Spatie Package Suite:**
- **Laravel Data 4**: Data transfer objects and validation
- **Laravel Media Library 11**: File upload and media management
- **Laravel Permission 6**: Role and permission system
- **Laravel Query Builder 6**: Advanced database query filtering
- **Laravel Schedule Monitor 3**: Cron job monitoring
- **Laravel Sluggable 3**: Automatic URL slug generation
- **Laravel Backup 9**: Database and file backup system
- **Laravel Collection Macros 8**: Extended collection methods
- **Laravel Ray 1**: Local debugging and inspection

**API & Integration:**
- **Laravel Orion 2**: Automatic REST API generation
- **Laravel API Response Helpers 2**: Standardized API responses
- **Intervention Image 3**: Image processing and manipulation
- **Maatwebsite Excel 3**: Excel import/export functionality
- **Flysystem AWS S3 v3**: Cloud storage integration

**Development & Debugging:**
- **Clockwork 5**: Performance profiling and debugging
- **Laravel Debugbar 3**: Development debugging toolbar
- **Laravel Impersonate 1**: User impersonation system
- **Prism PHP 0.78**: AI integration capabilities

### Frontend Package Ecosystem
**React & UI:**
- **React 19**: Latest React with concurrent features
- **React DOM 19**: DOM rendering with concurrent features
- **Headless UI 2**: Unstyled, accessible UI components
- **Radix UI**: Comprehensive primitive component library
- **Flowbite React**: React components built on Tailwind CSS (includes Pro components from https://flowbite.com/pro/)
- **Lucide React 0.475**: Beautiful icon library
- **Class Variance Authority**: Type-safe component variants
- **Tailwind Merge 3**: Intelligent CSS class merging

**Development Tools:**
- **Vite 6**: Fast build tool with HMR
- **TypeScript 5.7**: Static type checking
- **ESLint 9**: Code linting with modern flat config
- **Prettier 3**: Code formatting with plugins
- **Laravel Echo 2**: WebSocket client integration
- **Pusher JS 8**: Real-time WebSocket client

**Build & Optimization:**
- **@tailwindcss/vite 4**: Tailwind CSS 4 integration
- **Laravel Vite Plugin 1**: Laravel-specific Vite configuration
- **Concurrently 9**: Run multiple development processes
- **Ziggy 2**: Laravel route helpers in JavaScript

### Data Flow
1. Laravel routes return Inertia responses instead of JSON
2. Inertia.js handles client-side routing and state management
3. React components receive props directly from Laravel controllers
4. Form submissions go through Inertia.js back to Laravel controllers

## Code Structure

### Frontend Architecture (`resources/js/`)
- `app.tsx` - Main application entry point with Inertia setup
- `pages/` - Page components that map to Laravel routes
- `layouts/` - Reusable layout components (app, auth, settings)
- `components/` - Reusable UI components including shadcn/ui components
- `components/ui/` - shadcn/ui component library
- `hooks/` - Custom React hooks (appearance, mobile navigation)
- `lib/` - Utility functions (cn for class merging)
- `types/` - TypeScript type definitions

### Backend Structure
- `app/Http/Controllers/` - Laravel controllers organized by feature
- `app/Http/Controllers/Auth/` - Authentication controllers
- `app/Http/Controllers/Settings/` - User settings management
- `routes/` - Route definitions split by concern (web, auth, settings)
- `resources/views/app.blade.php` - Single HTML template for Inertia

### UI Component System
- Based on shadcn/ui with customizable theming
- Uses Radix UI primitives for accessibility
- Flowbite components (free and Pro versions) for additional Tailwind CSS utilities
- Tailwind CSS for styling with CSS variables for theming
- Components support both light and dark modes via `use-appearance` hook

### Authentication System
**Core Authentication:**
- **Laravel Authentication**: Built-in authentication with email verification
- **Sanctum API Authentication**: Token-based API authentication
- **Social Login**: OAuth integration via Laravel Socialite
- **User Impersonation**: Admin ability to impersonate other users
- **Password Reset**: Secure password reset with email tokens

**Client-Side Integration:**
- **Inertia.js Navigation**: Seamless client-side auth flow navigation
- **Protected Routes**: Middleware-based route protection (`auth`, `verified`)
- **Settings Management**: Profile and password management pages
- **Persistent Sessions**: Secure session management across requests

## TypeScript Configuration
- Path aliases configured: `@/*` maps to `resources/js/*`
- Ziggy integration for Laravel route helpers in TypeScript
- Strict TypeScript checking enabled
- React JSX automatic transform configured

## Advanced Features

### Server-Side Rendering (SSR)
- **Inertia SSR**: Full server-side rendering support
- **SEO Optimization**: Search engine friendly rendering
- **Performance**: Faster initial page loads
- **Commands**: `composer dev:ssr` for SSR development

### Real-Time Features
- **Laravel Reverb**: Built-in WebSocket server
- **Laravel Echo**: Client-side WebSocket integration
- **Pusher Integration**: Alternative WebSocket provider
- **Event Broadcasting**: Real-time event system

### API Capabilities
- **Dual Architecture**: Both Inertia and REST API support
- **Laravel Orion**: Automatic REST API generation
- **Sanctum Authentication**: Token-based API security
- **Standardized Responses**: Consistent API response format

### Data & Media Management
- **File Uploads**: Spatie Media Library integration
- **Image Processing**: Intervention Image for transformations
- **Excel Integration**: Import/export Excel files
- **Cloud Storage**: AWS S3 integration ready
- **Backup System**: Automated database and file backups

### Monitoring & Debugging
- **Laravel Pulse**: Application performance monitoring
- **Clockwork**: Request profiling and debugging
- **Debugbar**: Development debugging toolbar
- **Schedule Monitor**: Cron job monitoring and alerting
- **Ray Integration**: Local debugging and inspection

### Advanced User Features
- **Role-Based Permissions**: Spatie Permission system
- **User Impersonation**: Admin impersonation capabilities
- **Social Authentication**: OAuth provider integration
- **Profile Management**: Comprehensive user settings

## Testing Framework

### PHP Testing (Pest 3.8)
- **Modern Syntax**: Clean, expressive test syntax
- **Laravel Integration**: Laravel-specific testing features
- **Feature Tests**: Full application testing
- **Unit Tests**: Component-level testing
- **Database Testing**: In-memory SQLite for speed
- **API Testing**: Comprehensive API endpoint testing

### Commands
- `composer test` - Run all PHP tests
- `php artisan test` - Run tests via Artisan
- `npm run types` - TypeScript type checking

## Development Workflow
- Use `composer dev` for full development environment
- Frontend and backend run concurrently with hot reload
- Database migrations run automatically on setup
- Built-in appearance (dark/light mode) switching
- Comprehensive linting and formatting tools
- Real-time debugging with Clockwork and Debugbar