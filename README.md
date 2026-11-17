# TaskFlow

TaskFlow is a task management application with a Laravel 12 JSON API and a Vue 3 single-page frontend. The API uses Sanctum personal access tokens; the frontend uses Pinia, Vue Router, Axios, and Tailwind CSS.

## Requirements

- PHP 8.2+, Composer, and MySQL 8+ (or MariaDB)
- Node.js 20+ and npm

## Backend setup

Run these commands from the repository root:

```sh
composer install
copy .env.example .env
php artisan key:generate
```

Set `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` in `.env` for your MySQL database, then run:

```sh
php artisan migrate --seed
php artisan serve
```

The API is available at `http://127.0.0.1:8000/api`. The seeder creates:

- `admin@example.com` / `password`
- `alex@example.com` / `password`

Change these credentials before using a deployed environment.

## Frontend setup

In another terminal:

```sh
cd frontend
npm install
copy .env.example .env
npm run dev
```

Set `VITE_API_BASE_URL` to your backend URL if it differs from `http://127.0.0.1:8000/api`. Open the URL printed by Vite.

## API overview

All endpoints except registration and login require `Authorization: Bearer <token>`.

| Method | Endpoint | Purpose |
| --- | --- | --- |
| POST | `/api/register`, `/api/login` | Register or obtain an API token |
| POST | `/api/logout` | Revoke the current token |
| GET | `/api/me` | Current user profile |
| GET | `/api/dashboard` | Task totals and recent tasks |
| GET/POST | `/api/tasks` | List or create tasks |
| GET/PUT/DELETE | `/api/tasks/{task}` | Read, update, or delete a task |
| GET | `/api/users` | Search and paginate users |
| GET | `/api/users/{user}` | User details and task counts |

Task lists support `search`, `status`, `priority`, `due_date_from`, `due_date_to`, `sort_by`, `sort_direction`, `per_page`, and `page`. A user can see tasks they created or are assigned. Only the creator can edit task details or delete a task; an assignee can update its status. Authenticated users can assign tasks to any registered user.

## Implementation notes

- Laravel Form Requests validate every write and filter input; API Resources shape task and user responses.
- Task queries and dashboard aggregates share `TaskService`, including ownership scoping, whitelisted sorting, and overdue calculations.
- Sanctum issues revocable bearer tokens. Passwords are hashed, registration/login are rate-limited, and protected routes use `auth:sanctum`.
- The Vue app includes authenticated route guards, a Pinia auth store, reusable task form/table/modal/alert/pagination components, dashboard metrics, searchable/filterable task lists, and user details.
- Backend tests use Laravel's in-memory SQLite connection. Set up dependencies first, then run `php artisan test`.

## Local development and deployment

For local development, run the Laravel API and Vite dev server in separate terminals. Configure production HTTPS, set a private `APP_KEY`, use a dedicated MySQL database user, and set `FRONTEND_URL` to the deployed frontend origin for CORS. Build the frontend with `cd frontend && npm run build`. The frontend build output is in `frontend/dist`; deploy it to a static host or serve it through your preferred web server. Configure `VITE_API_BASE_URL` at build time.
