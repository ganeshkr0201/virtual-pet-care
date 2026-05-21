# 🐾 Virtual Pet Care System

A complete, production-ready Laravel 11 full-stack web application for managing pet care with daily reminders, health tracking, appointment scheduling, and real-time notifications.

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 11, PHP 8.3+ |
| Frontend | Blade + Tailwind CSS v4 + Alpine.js |
| Database | SQLite (dev) / MySQL (production) |
| Auth | Laravel Sanctum |
| Permissions | Spatie Laravel Permission |
| PDF | barryvdh/laravel-dompdf |
| Image | intervention/image-laravel |
| Queue | Database (configurable to Redis) |
| Charts | Chart.js (CDN) |
| Calendar | FullCalendar.js (CDN) |
| Alerts | SweetAlert2 (CDN) |

---

## Features

### Authentication
- Register / Login / Logout
- Forgot & Reset Password
- Email Verification
- Role-based access (Admin / Pet Owner)

### Pet Management
- Add, edit, delete pets with photo upload
- Species, breed, gender, age, weight tracking
- Vet information, allergies, medical history
- Activity level, feeding schedule
- Pet image gallery

### Daily Reminder System
- 10 reminder types: feeding, walking, exercise, grooming, medication, vet appointment, vaccination, training, water, other
- Daily / weekly / monthly repeat schedules
- Mark complete, snooze reminders
- Missed reminder tracking
- Email + push notification support

### Dashboard
- Today's reminder progress
- Weekly activity bar chart
- Monthly completion rate donut chart
- Upcoming appointments & vaccinations
- Quick pet overview grid

### Health Tracker
- Medical records timeline (checkup, illness, surgery, prescription, weight log, symptom)
- Vaccination records with due-date alerts
- Weight history line chart
- File attachment support (PDF, images)

### Calendar
- FullCalendar.js integration
- Reminders + appointments in one view
- Month / week / list views

### Notifications
- In-app notification bell
- Email notifications (queued)
- Vaccination due alerts (scheduler)
- Mark read / mark all read

### Admin Panel
- Admin dashboard with platform stats
- User management (activate/deactivate)
- Monthly analytics charts
- Reports page

### REST API (v1)
- JWT-style token auth via Sanctum
- `/api/v1/auth/register`, `/login`, `/logout`, `/me`
- `/api/v1/pets` — full CRUD
- `/api/v1/reminders` — full CRUD + complete/snooze
- `/api/v1/dashboard`
- `/api/v1/notifications`

---

## Installation

### Requirements
- PHP 8.3+
- Composer
- Node.js 18+
- SQLite (dev) or MySQL 8+ (production)

### Steps

```bash
# 1. Clone / enter project
cd virtual-pet-care

# 2. Install PHP dependencies
composer install

# 3. Install Node dependencies
npm install

# 4. Copy environment file
cp .env.example .env

# 5. Generate app key
php artisan key:generate

# 6. Configure database in .env
# For SQLite (default, no setup needed):
# DB_CONNECTION=sqlite

# For MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=virtual_pet_care
# DB_USERNAME=root
# DB_PASSWORD=secret

# 7. Run migrations and seed demo data
php artisan migrate --seed

# 8. Create storage symlink
php artisan storage:link

# 9. Build frontend assets
npm run build

# 10. Start development server
php artisan serve
```

Visit: **http://localhost:8000**

---

## Demo Accounts

| Role | Email | Password |
|---|---|---|
| Admin | admin@virtualpetcare.com | password |
| Pet Owner | demo@virtualpetcare.com | password |

---

## Queue Workers

For email notifications and background jobs:

```bash
php artisan queue:work
```

## Scheduler (Cron)

Add to your server's crontab:

```
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

Scheduled tasks:
- `00:01` — Generate daily reminder logs
- `23:55` — Mark missed reminders
- Every 5 min — Send reminder notifications
- `09:00` — Send vaccination due alerts

---

## Running Tests

```bash
php artisan test
```

22 tests, 42 assertions — all passing.

---

## Project Structure

```
app/
├── Actions/              # Single-action classes
├── Console/Commands/     # Artisan commands (scheduler tasks)
├── Http/
│   ├── Controllers/
│   │   ├── Api/          # REST API controllers
│   │   ├── Admin/        # Admin panel controllers
│   │   └── Auth/         # Authentication controllers
│   └── Resources/        # API resources (extendable)
├── Jobs/                 # Queued jobs
├── Models/               # Eloquent models (11 models)
├── Notifications/        # Laravel notifications
├── Policies/             # Authorization policies
├── Providers/            # Service providers
├── Repositories/         # Data access layer
├── Services/             # Business logic layer
└── Traits/               # Reusable traits

database/
├── factories/            # Model factories (4)
├── migrations/           # 17 migrations
└── seeders/              # Roles, admin user, demo data

resources/
├── css/app.css           # Tailwind v4 styles
├── js/app.js             # Alpine.js + app logic
└── views/
    ├── admin/            # Admin panel views
    ├── appointments/     # Appointment CRUD views
    ├── auth/             # Login, register, password reset
    ├── dashboard/        # Main dashboard
    ├── health/           # Health tracker views
    ├── layouts/          # App + guest layouts
    ├── notifications/    # Notification center
    ├── pages/            # Public pages (landing, about, contact)
    ├── pets/             # Pet CRUD views
    ├── profile/          # Profile settings
    └── reminders/        # Reminder CRUD + calendar

routes/
├── web.php               # 66 web routes
├── api.php               # 19 API routes
└── console.php           # Scheduler definitions
```

---

## API Reference

All API routes are prefixed with `/api/v1`. Authenticated routes require:
```
Authorization: Bearer {token}
```

### Auth
| Method | Endpoint | Description |
|---|---|---|
| POST | `/auth/register` | Register new user |
| POST | `/auth/login` | Login, returns token |
| POST | `/auth/logout` | Revoke token |
| GET | `/auth/me` | Get current user |

### Pets
| Method | Endpoint | Description |
|---|---|---|
| GET | `/pets` | List user's pets |
| POST | `/pets` | Create pet |
| GET | `/pets/{id}` | Get pet details |
| PUT | `/pets/{id}` | Update pet |
| DELETE | `/pets/{id}` | Delete pet |

### Reminders
| Method | Endpoint | Description |
|---|---|---|
| GET | `/reminders` | List reminders |
| POST | `/reminders` | Create reminder |
| GET | `/reminders/{id}` | Get reminder |
| PUT | `/reminders/{id}` | Update reminder |
| DELETE | `/reminders/{id}` | Delete reminder |
| POST | `/reminders/{id}/complete` | Mark complete |
| POST | `/reminders/{id}/snooze` | Snooze reminder |

---

## Deployment

### Environment Variables (Production)

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=virtual_pet_care
DB_USERNAME=dbuser
DB_PASSWORD=strongpassword

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@yourdomain.com

QUEUE_CONNECTION=redis
CACHE_STORE=redis
SESSION_DRIVER=redis

REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

### Production Commands

```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
npm run build
```

---

## Security

- CSRF protection on all forms
- XSS prevention via Blade auto-escaping
- SQL injection prevention via Eloquent ORM
- Authorization policies on all resource operations
- Rate limiting on API routes
- Secure file upload validation (type + size)
- Password hashing with bcrypt (12 rounds)
- Sanctum token authentication for API

---

## License

MIT License — free to use for personal and commercial projects.
