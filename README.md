# Barbershop Premium - Digital Booking System

A comprehensive Laravel 12 application for managing a premium barbershop business with appointment booking, service management, and customer relationship features.

## Features

### Core Functionality
- ✅ **Appointment Booking System** - Online booking with conflict detection and availability management
- ✅ **Service Management** - Multiple service types (haircut, beard, styling, coloring, treatment, packages)
- ✅ **Barber Profiles** - Expert barber profiles with ratings, specializations, and availability
- ✅ **Customer Management** - Track customer visit history and preferences
- ✅ **Working Hours Management** - Flexible scheduling for barbers
- ✅ **Time Slot Management** - Automated availability calculation

### Technical Features
- ✅ **PSR-12 Compliant** - Following PHP coding standards
- ✅ **SOLID Principles** - Clean architecture with separation of concerns
- ✅ **Repository Pattern** - Data access abstraction
- ✅ **Service Layer** - Business logic separation
- ✅ **DTOs** - Type-safe data transfer objects
- ✅ **Eloquent ORM** - No raw SQL queries
- ✅ **Form Requests** - Centralized validation
- ✅ **Policies** - Authorization gates
- ✅ **Queue Jobs** - Async processing for notifications
- ✅ **Enums** - Type-safe constants (PHP 8.2+)

## Technology Stack

- **Framework**: Laravel 12.37.0
- **PHP**: 8.2+
- **Database**: MySQL 8.0 / SQLite (testing)
- **Queue**: Redis with Laravel Horizon
- **Frontend**: Blade Templates with Tailwind CSS
- **API**: Laravel Sanctum for authentication
- **Testing**: PHPUnit 11.4
- **Code Quality**: Laravel Pint (PSR-12)

## Quick Start

```bash
# Clone and install
git clone https://github.com/igestaos-eng/barbearia.git
cd barbearia
composer install
cp .env.example .env
php artisan key:generate

# Setup database
php artisan migrate
php artisan db:seed

# Start server
php artisan serve
```

Visit: http://localhost:8000

## Docker Development

```bash
docker-compose up -d
```

Access at: http://localhost:8000

## Project Structure

```
app/
├── DTOs/           # Data Transfer Objects
├── Enums/          # Type-safe enumerations
├── Models/         # Eloquent models
├── Repositories/   # Data access layer
├── Services/       # Business logic layer
└── Http/           # Controllers & Requests

database/
├── migrations/     # Database schema
├── factories/      # Test data factories
└── seeders/        # Database seeders

resources/views/    # Blade templates
routes/             # Application routes
tests/              # Feature & Unit tests
```

## Testing

```bash
php artisan test
./vendor/bin/pint  # Code formatting
```

## License

MIT License

---

**Built with Laravel 12**
