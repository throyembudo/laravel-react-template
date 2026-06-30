# Laravel + React Template

A full-stack application template with a Laravel API backend and a React (Vite) frontend.

## Project Structure

```
├── src-backend/       # Laravel API (PHP)
├── src-frontend/      # React SPA (Vite)
├── docker/            # Docker configuration files
├── docker-compose.yaml
└── README.md
```

## Prerequisites

- Docker & Docker Compose
- PHP 8.0+ (for local development without Docker)
- Composer
- Node.js 16+ & npm

## Getting Started

### 1. Clone the repository

```bash
git clone https://github.com/throyembudo/laravel-react-template.git
cd laravel-react-template
```

### 2. Backend Setup (src-backend)

```bash
cd src-backend

# Copy environment file
cp .env.example .env

# Install dependencies
composer install

# Generate application key
php artisan key:generate

# Run database migrations
php artisan migrate
```

#### Backend Environment Variables

Update `src-backend/.env` with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=root
```

For Google OAuth (optional):

```env
GOOGLE_CLIENT_ID=your-client-id
GOOGLE_CLIENT_SECRET=your-client-secret
GOOGLE_REDIRECT_URI=your-redirect-uri
```

### 3. Frontend Setup (src-frontend)

```bash
cd src-frontend

# Install dependencies
npm install

# Copy environment file
cp .env.example .env
```

#### Frontend Environment Variables

Update `src-frontend/.env` to point to your backend API:

```env
VITE_API_BASE_URL=http://localhost:8000
```

## Running the Application

### Option A: Using Docker

```bash
# From the project root
docker-compose up -d --build
```

This starts:
- **Nginx** — reverse proxy on port 80
- **API** — Laravel PHP backend
- **Database** — MySQL 8.0 on port 3306

### Option B: Running Locally (without Docker)

**Terminal 1 — Backend:**

```bash
cd src-backend
php artisan serve
```

The API will be available at `http://localhost:8000`.

**Terminal 2 — Frontend:**

```bash
cd src-frontend
npm run dev
```

The React app will be available at `http://localhost:3000`.

## Running Tests

### Backend Tests

```bash
cd src-backend
php artisan test
```

Or run specific test suites:

```bash
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature
```

### Static Analysis (PHPStan)

```bash
cd src-backend
./vendor/bin/phpstan analyse --configuration=phpstan.neon --memory-limit=512M
```

## API Endpoints

### Public Routes

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/signup` | Register a new user |
| POST | `/api/login` | Login with credentials |
| GET | `/api/auth` | Get Google OAuth redirect URL |
| GET | `/api/auth/callback` | Handle Google OAuth callback |

### Protected Routes (requires authentication)

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/logout` | Logout current user |
| GET | `/api/user` | Get authenticated user |
| GET | `/api/users` | List all users (paginated) |
| POST | `/api/users` | Create a new user |
| GET | `/api/users/{id}` | Get a specific user |
| PUT | `/api/users/{id}` | Update a user |
| DELETE | `/api/users/{id}` | Delete a user |

## Tech Stack

**Backend:**
- Laravel 9
- PHP 8.0+
- Laravel Sanctum (API authentication)
- Laravel Socialite (Google OAuth)
- MySQL 8.0
- PHPStan + Larastan (static analysis)
- PHPUnit (testing)

**Frontend:**
- React 18
- Vite
- React Router
- Axios
- Tailwind CSS
