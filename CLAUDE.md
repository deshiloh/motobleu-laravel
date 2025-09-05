# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel 10 application for MotoBleue Paris - a transportation/booking management system. It's a full-featured web application with admin and front-end interfaces for managing reservations, companies, drivers (pilotes), passengers, and billing.

## Key Technologies & Architecture

- **Framework**: Laravel 10.48.22 (PHP 8.1+)
- **Development Environment**: Laravel Sail (Docker-based)
- **Frontend**: Livewire v2.10 with Alpine.js and TailwindCSS
- **UI Components**: WireUI v1.17 for enhanced UI components
- **Database**: MySQL/SQLite with Eloquent ORM
- **Build Tools**: Vite for asset compilation
- **Authentication**: Laravel Sanctum
- **Permissions**: Spatie Laravel Permission
- **Export**: Maatwebsite Excel for data exports
- **Calendar Integration**: Spatie Google Calendar
- **Invoicing**: LaravelDaily Laravel Invoices
- **Error Tracking**: Sentry

## Development Commands

**IMPORTANT**: This project uses Laravel Sail for local development. All PHP/Laravel commands should be prefixed with `./vendor/bin/sail` or use the `sail` alias.

### Laravel Sail Commands
```bash
# Start the development environment (Docker containers)
./vendor/bin/sail up -d

# Stop the development environment
./vendor/bin/sail down

# Create sail alias for easier usage (run once)
alias sail='./vendor/bin/sail'

# Run database migrations
./vendor/bin/sail artisan migrate

# Seed database
./vendor/bin/sail artisan db:seed

# Clear caches
./vendor/bin/sail artisan cache:clear
./vendor/bin/sail artisan config:clear
./vendor/bin/sail artisan route:clear
./vendor/bin/sail artisan view:clear

# Run tests
./vendor/bin/sail artisan test
./vendor/bin/sail test

# Static analysis
./vendor/bin/sail php ./vendor/bin/phpstan analyse

# Generate IDE helpers
./vendor/bin/sail artisan ide-helper:generate

# Access container shell
./vendor/bin/sail shell

# View logs
./vendor/bin/sail logs
```

### Asset Compilation
```bash
# Install Node dependencies (inside Sail container)
./vendor/bin/sail npm install

# Development mode with hot reload
./vendor/bin/sail npm run dev

# Production build
./vendor/bin/sail npm run build
```

## Core Business Models & Relationships

- **User**: Main user entity with role-based permissions
- **Entreprise**: Companies that book transportation services
- **Reservation**: Core booking entity linking passengers, companies, and drivers
- **Passager**: Passengers associated with users and companies
- **Pilote**: Drivers who fulfill reservations
- **Facture**: Invoice/billing system for companies
- **AdresseReservation**: Addresses for pickup/dropoff locations

### Key Relationships
- Users belong to multiple Entreprises (many-to-many)
- Reservations are linked to Passagers, Pilotes, and Entreprises
- Users can have multiple Passagers and AdresseReservations
- Factures group multiple Reservations for billing

## Architecture Patterns

### Livewire Components Structure
Components are organized by feature areas:
- `Admin/`: Administrative interface components
- `Front/`: Customer-facing interface components  
- `Account/`: User account management
- `Reservation/`: Booking management
- `Entreprise/`: Company management
- `Stats/`: Dashboard and analytics

### Route Organization
Routes are split into logical files:
- `web.php`: Public routes and authentication
- `admin.php`: Administrative routes
- `front.php`: Customer interface routes
- `api.php`: API endpoints

### Middleware & Security
- Custom `Localization` middleware for internationalization
- Role-based access control using Spatie Permission
- Authentication handled by Laravel Sanctum

## Custom Artisan Commands

- `app:import`: Data import functionality
- `app:reload-password-local`: Local password reset utility
- `calendar:credentials`: Google Calendar OAuth setup

## Testing

Tests are organized using Laravel's standard structure:
- Unit tests in `tests/Unit/`
- Feature tests in `tests/Feature/`
- PHPUnit configuration in `phpunit.xml`

## File Structure Notes

- Custom Enums in `app/Enum/` for status management
- Export classes in `app/Exports/` for Excel/PDF generation
- Email templates in `resources/views/emails/`
- Livewire views follow the pattern: `resources/views/livewire/{component-path}.blade.php`

## Environment & Configuration

- Multiple environment files: `.env`, `.env.testing`
- Google Calendar integration requires OAuth credentials
- Sentry error tracking configured
- Laravel Scout for search functionality
- Internationalization support (French/English)

## Important Business Logic

- Reservation workflow with multiple statuses (ReservationStatus enum)
- Commission calculations for drivers
- Multi-company user management
- Role-based permissions (admin, super admin, regular users)
- Billing system with PDF invoice generation
- Email notifications for reservation lifecycle events