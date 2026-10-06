# Real Estate SMA

Luxury real estate listings and agent management for San Miguel de Allende, Mexico.

## About

Real Estate SMA is a real-estate agency platform built as a certification project (CDA — Concepteur
Développeur d'Applications). It lets visitors browse and search luxury listings and submit inquiries,
gives agents a back office to manage their listings and lead pipeline, and gives administrators a
control panel for users, agents, property approval and lead assignment.

## Features

**Public site**
- Homepage with a featured-listings hero slider and quick search.
- Browse and filter listings (keyword, type, area, bedrooms, price, sort).
- Property detail page with photo gallery, facts and a contact/inquiry form.
- General contact page and static pages (about, legal notice, privacy policy).
- GDPR: explicit consent required on every lead-collecting form (recorded with a timestamp).

**Agent back office**
- Create and edit own listings (photos, categories, dual USD/MXN pricing).
- Track assigned leads through a pipeline (`new → contacted → qualified → closed / lost`).

**Admin back office**
- User management: enable/disable logins (with a last-active-admin safety rule).
- Agent account creation (login + public profile created atomically).
- Property approval workflow (approve / unpublish / delete).
- Lead oversight and assignment (assign, reassign, unassign any lead).

## Tech stack

- **Laravel 13** on **PHP 8.4**
- **MySQL / MariaDB** (SQLite supported for local dev and tests)
- **Blade** + **Tailwind CSS v4** + **BlatUI** (shadcn/ui-style component library)
- **Alpine.js**, **Vite**
- **Pest** for testing

## Requirements

- PHP 8.4+
- Composer
- Node.js + npm
- MySQL / MariaDB (or SQLite for quick local setup)

## Installation

```bash
git clone <repository-url>
cd realestatesma

composer install
cp .env.example .env
php artisan key:generate

# Configure your database in .env (DB_CONNECTION=sqlite for a quick start, or mysql)

php artisan migrate --seed
php artisan storage:link

npm install
npm run build

php artisan serve
```

The seeder creates demo categories, six approved listings (with generated placeholder photos),
a demo agent profile and an admin account.

## Demo accounts

| Role | Email | Password |
|---|---|---|
| Admin | `admin@example.com` | `password` |

Agent accounts are created from the admin area (`/admin/agents`) — each creation generates a login
(email + password) plus the linked public profile. The seeded demo agent `Sofia Ramírez`
(`demo.agent@example.com`) is a public profile without a login.

## Testing

```bash
vendor/bin/pest
# or
php artisan test
```
