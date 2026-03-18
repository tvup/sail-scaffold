# Contributing to Sail Scaffold

Thank you for considering contributing to Sail Scaffold! This document provides guidelines for contributing.

## Getting Started

1. Fork the repository
2. Clone your fork locally
3. Install dependencies:
   ```bash
   composer install
   npm install
   cp .env.example .env
   php artisan key:generate
   php artisan migrate
   php artisan db:seed
   ```
4. Create a feature branch: `git checkout -b feature/my-feature`

## Development

Run the development server:

```bash
composer run dev
```

## Code Style

This project uses [Laravel Pint](https://laravel.com/docs/pint) for code formatting. Run it before committing:

```bash
vendor/bin/pint
```

## Testing

All changes must include tests. Run the test suite with:

```bash
php artisan test --compact
```

## Pull Requests

- Keep PRs focused on a single change
- Include tests for new functionality
- Ensure all tests pass before submitting
- Follow the existing code style and conventions

## Reporting Issues

Use [GitHub Issues](https://github.com/tvup/sail-scaffold/issues) to report bugs or suggest features. Include steps to reproduce for bug reports.
