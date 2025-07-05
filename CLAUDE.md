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
- **Vite**: Frontend build tool with HMR support

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
- Tailwind CSS for styling with CSS variables for theming
- Components support both light and dark modes via `use-appearance` hook

### Authentication Flow
- Standard Laravel authentication with email verification
- Inertia.js handles client-side navigation for auth flows
- Protected routes use Laravel middleware (`auth`, `verified`)
- Settings pages for profile and password management

## TypeScript Configuration
- Path aliases configured: `@/*` maps to `resources/js/*`
- Ziggy integration for Laravel route helpers in TypeScript
- Strict TypeScript checking enabled
- React JSX automatic transform configured

## Development Workflow
- Use `composer dev` for full development environment
- Frontend and backend run concurrently with hot reload
- Database migrations run automatically on setup
- Built-in appearance (dark/light mode) switching