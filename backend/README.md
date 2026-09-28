# Government Service Portal

Government Service Portal is a Laravel-based web application for managing citizen applications, departmental services, fee payments, notices, feedback, and administrative review workflows.

## Features

- Citizen registration and authentication
- Service browsing and application submission
- Document upload for applications
- QR-based payment flow with payment statement upload
- Payment verification status tracking
- OTP-based forgot password flow
- Citizen and admin dashboards
- Department, service, notice, and feedback management
- Role-based access control for citizen and admin users

## Project Structure (Monorepo)

The project is organized into clean, dedicated `frontend/` and `backend/` directories:

```text
government-service/
├── frontend/                     # Client-side UI, Templates & Styling
│   ├── views/                   # Blade Templates (Admin, Citizen, Auth, Layouts, Emails)
│   ├── css/                     # Custom Styles & Animations (custom.css, app.css)
│   ├── js/                      # Interactive Scripts (Lucide icons, live validation)
│   ├── images/                  # Municipality logos, emblems, building photos
│   └── package.json             # Frontend assets configuration
│
├── backend/                      # Laravel Server Core & APIs
│   ├── app/                     # Controllers, Models, Middleware, Notifications
│   ├── bootstrap/               # Application bootstrap & middleware aliases
│   ├── config/                  # App Settings & View Resolution config
│   ├── database/                # Migrations, Seeders, Factories
│   ├── public/                  # Web Server Entry (index.php) + Asset junctions
│   ├── routes/                  # Route definitions (web.php, auth.php, console.php)
│   ├── storage/                 # Uploaded files, logs, and framework caches
│   ├── vendor/                  # Composer dependencies
│   ├── artisan                  # Artisan CLI
│   └── .env                     # Database & Gmail SMTP configuration
│
└── artisan                       # Root-level CLI proxy (run commands directly from root)
```

## Running the Application

You can run the development server directly from the root directory or inside `backend/`:

```bash
# Option 1: Directly from the root directory
php artisan serve

# Option 2: From the backend directory
cd backend
php artisan serve
```

The application will be available at `http://127.0.0.1:8000`.

## Prerequisites

- PHP 8.2 or newer
- Composer
- Node.js and npm
- MySQL 8+

## Installation

Clone the repository and install dependencies:

```bash
composer install
npm install
```

Copy the environment file and generate an application key:

```bash
copy .env.example .env
php artisan key:generate
```

Update `.env` with your database and mail settings.

## Environment Setup

Important `.env` values:

```env
APP_URL=http://localhost:8000
DB_CONNECTION=mysql
DB_DATABASE=government_service
DB_HOST=127.0.0.1
DB_PORT=3306
DB_USERNAME=root
DB_PASSWORD=your_password

MAIL_MAILER=smtp
MAIL_SCHEME=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_FROM_ADDRESS=your_email@gmail.com
MAIL_FROM_NAME="Government Service"
```

## Database Setup

Run migrations and seeders:

```bash
php artisan migrate
php artisan db:seed
```

## Development

Start the Laravel server:

```bash
php artisan serve
```

In another terminal, start the frontend build watcher:

```bash
npm run dev
```

## Testing

Run the test suite:

```bash
php artisan test
```

## Key User Flows

### Citizen

- Register and log in
- Browse available government services
- Submit an application with required documents
- Pay fees through QR-based flow
- Upload payment statement image
- Track payment verification status

### Password Reset

- Enter email on the forgot password page
- Receive a 6-digit OTP by email
- Verify OTP and set a new password

### Admin

- Review submitted applications
- Update application status
- Inspect uploaded documents and payment statements
- Manage services, departments, notices, and feedback

## Project Folder Structure

The project follows a clean, modular, role-separated Laravel architecture:

```text
government-service/
├── app/
│   ├── Enums/                     # Status and role definitions
│   │   ├── ApplicationStatus.php  # PENDING, UNDER_REVIEW, APPROVED, REJECTED, etc.
│   │   ├── PaymentStatus.php      # PENDING, VERIFIED, REJECTED
│   │   └── UserRole.php           # ADMIN, CITIZEN
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/             # Admin portal controllers (Applications, Departments, Services, etc.)
│   │   │   ├── Citizen/           # Citizen portal controllers (Applications, Payments, Services, etc.)
│   │   │   ├── Auth/              # Authentication & OTP controllers (Login, Register, Password, etc.)
│   │   │   ├── Api/               # API endpoints (Payment APIs, etc.)
│   │   │   └── ProfileController.php
│   │   ├── Middleware/            # Role guards (AdminMiddleware, CitizenMiddleware)
│   │   └── Requests/              # Form validation requests separated by role (Admin, Citizen, Auth)
│   ├── Models/                    # Eloquent models (Application, Service, Department, Payment, etc.)
│   ├── Notifications/             # Email and system notifications (OTP, status updates)
│   ├── Providers/                 # Service providers
│   ├── Services/                  # Business logic layer (ApplicationService, PaymentService, NoticeService)
│   └── Traits/                    # Reusable traits (File upload handlers, audit logs)
├── bootstrap/                     # Framework bootstrap & middleware configuration
├── config/                        # Application configurations (auth, database, mail, etc.)
├── database/
│   ├── factories/                 # Model factories for testing and seeding
│   ├── migrations/                # Database schema migrations
│   └── seeders/                   # Database seeders (Admin, Citizen, Services, Departments)
├── public/                        # Public entry point and static assets
│   ├── css/                       # Custom CSS (custom.css - Barhadashi styling)
│   ├── images/                    # Official emblems, backgrounds, and static media
│   └── storage/                   # Symlink to storage/app/public for uploaded files
├── resources/
│   ├── css/                       # Source CSS / Tailwind entries
│   ├── js/                        # Source JavaScript / Vite build entries
│   └── views/                     # Blade view templates
│       ├── admin/                 # Admin dashboard and management views
│       ├── citizen/               # Citizen portal views (Applications, Payments, Services)
│       ├── auth/                  # Authentication views (Login, Register, Forgot Password, Reset)
│       ├── layouts/               # Master layouts (admin.blade.php, citizen.blade.php, guest.blade.php)
│       ├── components/            # Reusable Blade UI components (alerts, modals, inputs)
│       └── emails/                # Email notification templates (OTP, notifications)
├── routes/
│   ├── web.php                    # Web routes organized by role prefixes (/admin, /citizen)
│   ├── auth.php                   # Authentication routes (login, register, password reset)
│   └── console.php                # Artisan console commands
├── storage/                       # Application storage (logs, uploaded citizen documents, QR codes)
└── tests/                         # Pest and PHPUnit automated test suites
```

## Notes

- Payment receipt and verification screens depend on a running MySQL service.
- OTP password reset uses Gmail SMTP in `.env`.
- If you change `.env`, run `php artisan config:clear` before testing mail or auth flows.

## License

This project is built with Laravel and follows the license terms of the underlying framework and project assets.
