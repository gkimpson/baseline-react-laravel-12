# Pro Clubs v3 - Development Guidelines

## Project Overview
Pro Clubs v3 is a web application for managing Pro Clubs teams, players, and matches. The application provides features for player search, transfers, match history, statistics, leaderboards, and club management.

## Tech Stack

### Backend
- **Framework**: Laravel 12.x
- **PHP Version**: 8.2
- **Database**: MySQL 8.x
- **API**: Laravel Sanctum for API authentication
- **Queue Management**: Laravel Horizon
- **Monitoring**: Laravel Pulse, Laravel Telescope
- **Authentication**: Laravel Socialite for OAuth

### Frontend
- **Framework**: React 19 with TypeScript
- **Build Tool**: Vite
- **CSS Framework**: TailwindCSS 4.0
- **UI Components**: Radix UI components
- **State Management**: React Hooks
- **Server-Side Rendering**: Inertia.js
- **Form Validation**: Zod
- **Charts**: Recharts
- **Animations**: Framer Motion

### Testing
- **PHP Testing**: Pest PHP (PHPUnit wrapper)
- **JavaScript Testing**: Jest

## Development Environment Setup

### Prerequisites
- PHP 8.2+
- Composer
- Node.js (latest LTS version)
- npm or yarn
- MySQL 8.x

### Installation Steps
1. Clone the repository
2. Copy `.env.example` to `.env` and configure your environment variables
3. Run `composer install` to install PHP dependencies
4. Run `npm install` to install JavaScript dependencies
5. Run `php artisan key:generate` to generate an application key
6. Run `php artisan migrate` to set up the database
7. Run `npm run dev` to start the development server

### Development Commands
- `npm run dev`: Start the Vite development server
- `php artisan serve`: Start the Laravel development server
- `php artisan queue:work`: Start the queue worker
- `npm run build`: Build the frontend assets for production
- `npm run lint`: Run ESLint to check for code issues
- `npm run format`: Run Prettier to format code
- `php artisan test`: Run PHP tests

## Coding Standards and Conventions

### PHP
- Follow PSR-12 coding standards
- Use type hints for method parameters and return types
- Use Laravel's built-in features and conventions
- Organize code into appropriate namespaces
- Use dependency injection where possible

### JavaScript/TypeScript
- Use TypeScript for type safety
- Follow ESLint and Prettier configurations
- Use functional components and hooks for React
- Organize imports (automatically handled by Prettier)
- Use async/await for asynchronous operations
- Use proper TypeScript interfaces for type definitions

### CSS/Styling
- Use TailwindCSS utility classes
- Follow the component-based styling approach
- Use the clsx or cn utility for conditional class names
- Follow the project's color scheme and design system

### Git Workflow
- Use feature branches for new features
- Create pull requests for code reviews
- Write meaningful commit messages
- Keep commits focused and atomic

## Project Structure

### Backend (Laravel)
- `app/`: Contains the core code of the application
  - `Http/Controllers/`: Controllers for handling HTTP requests
  - `Models/`: Eloquent models representing database tables
  - `Services/`: Business logic services
  - `Providers/`: Service providers
- `config/`: Configuration files
- `database/`: Database migrations, seeders, and factories
- `routes/`: Route definitions
  - `web.php`: Web routes
  - `api.php`: API routes
  - `auth.php`: Authentication routes
- `storage/`: Application storage (logs, cache, etc.)
- `tests/`: Test files
  - `Feature/`: Feature tests
  - `Unit/`: Unit tests

### Frontend (React)
- `resources/js/`: JavaScript/TypeScript code
  - `components/`: Reusable UI components
  - `layouts/`: Page layouts
  - `pages/`: Page components
  - `lib/`: Utility functions and helpers
  - `types/`: TypeScript type definitions
- `resources/css/`: CSS files
- `public/`: Publicly accessible files

## Testing Guidelines

### PHP Testing
- Write feature tests for controllers and API endpoints
- Write unit tests for services and models
- Use Pest PHP's expressive syntax
- Use database factories for test data
- Use mocks and stubs for external dependencies

### JavaScript Testing
- Write unit tests for utility functions
- Write component tests for React components
- Use Jest's snapshot testing for UI components
- Mock API calls and external dependencies

## Deployment Process
- Build frontend assets with `npm run build`
- Run database migrations with `php artisan migrate`
- Clear caches with `php artisan optimize:clear`
- Restart queue workers with `php artisan queue:restart`

## Performance Considerations
- Use Laravel's caching mechanisms
- Optimize database queries
- Use eager loading for Eloquent relationships
- Minimize JavaScript bundle size
- Use code splitting for large components
- Optimize images and assets

## Security Best Practices
- Validate all user input
- Use Laravel's CSRF protection
- Use prepared statements for database queries
- Implement proper authentication and authorization
- Keep dependencies up to date
- Follow OWASP security guidelines

## Additional Resources
- [Laravel Documentation](https://laravel.com/docs)
- [React Documentation](https://react.dev/)
- [TypeScript Documentation](https://www.typescriptlang.org/docs/)
- [TailwindCSS Documentation](https://tailwindcss.com/docs)
- [Inertia.js Documentation](https://inertiajs.com/)
