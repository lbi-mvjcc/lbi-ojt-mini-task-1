# Task Management System

A modern task management system built with Laravel and React.js featuring role-based access control, automatic task assignment, and a beautiful green color theme.

## Features

✨ **Role-Based Access Control**
- Customer, Frontend Developer, Backend Developer, Server Administrator roles
- Secure authentication with Laravel Sanctum
- Protected routes and API endpoints

🎯 **Automatic Task Assignment**
- Tasks automatically assigned based on category
- Frontend tasks → Frontend Developer
- Backend tasks → Backend Developer
- Server tasks → Server Administrator

🎨 **Modern Green Theme**
- Beautiful gradient designs
- Responsive layout
- Clean and intuitive interface

🔒 **Privacy & Security**
- Customers cannot see assigned developers
- Developers can see customer information
- Role-based data access
- Secure API authentication

📊 **Project Management**
- Support for multiple projects
- Fixed team structure per project
- Developers can work on multiple projects

## Tech Stack

**Backend:**
- Laravel 12
- PHP 8.2+
- Laravel Sanctum
- SQLite/MySQL/PostgreSQL

**Frontend:**
- React 18
- React Router DOM
- Axios
- Custom CSS (Green Theme)

## Quick Start

```bash
# Install dependencies
composer install
npm install

# Setup environment
copy .env.example .env
php artisan key:generate

# Setup database
php artisan migrate
php artisan db:seed

# Start servers
php artisan serve    # Terminal 1
npm run dev          # Terminal 2
```

Visit: **http://localhost:8000**

## Test Accounts

Password for all: `password`

**Customers:**
- customer1@example.com
- customer2@example.com
- customer3@example.com

**Developers:**
- frontend0@example.com (Frontend Developer)
- backend0@example.com (Backend Developer)
- server0@example.com (Server Administrator)

## Documentation

- [Setup Guide](SETUP_GUIDE.md) - Detailed installation instructions
- [Full Documentation](DOCUMENTATION.md) - Complete system documentation

## System Overview

### Database Structure
- **users** - All system users with roles
- **projects** - Development projects
- **project_members** - Team assignments (1 Frontend, 1 Backend, 1 Server per project)
- **tasks** - Customer tasks with automatic assignment

### User Roles

**Customer**
- Create tasks
- View own tasks only
- Select task category
- Cannot see assigned developers

**Frontend Developer**
- Receive frontend tasks
- Update task status
- View customer information
- Can work on multiple projects

**Backend Developer**
- Receive backend tasks
- Update task status
- View customer information
- Can work on multiple projects

**Server Administrator**
- Receive server tasks
- Update task status
- View customer information
- Can work on multiple projects

### Automatic Assignment Logic

When a customer creates a task:
1. Customer selects task category (Frontend/Backend/Server)
2. System identifies project's developer for that category
3. Task automatically assigned to appropriate developer
4. Developer sees task in their dashboard
5. Customer sees task status updates (developer hidden)

## API Endpoints

```
POST   /api/register              - Register new user
POST   /api/login                 - Login user
POST   /api/logout                - Logout user
GET    /api/me                    - Get current user
GET    /api/projects              - List projects
GET    /api/tasks                 - List user's tasks
POST   /api/tasks                 - Create task (customer only)
GET    /api/tasks/{id}            - Get task details
PATCH  /api/tasks/{id}/status     - Update status (developer only)
```

## Key Constraints

- One Frontend, Backend, and Server Admin per project
- Developers can work on multiple projects
- Customers cannot choose or see developers
- Users can only access their own tasks
- Automatic assignment based on task category

## Color Theme

**Primary Colors:**
- Primary Green: `#059669`
- Dark Green: `#047857`
- Light Green: `#d1fae5`
- Background: `#f0f9f4`

**Features:**
- Green gradient navbar
- Green-themed buttons and badges
- Consistent color scheme throughout
- Professional and modern design

## Project Structure

```
├── app/
│   ├── Http/Controllers/Api/    # API Controllers
│   ├── Http/Middleware/         # Custom Middleware
│   └── Models/                  # Eloquent Models
├── database/
│   ├── migrations/              # Database Migrations
│   └── seeders/                 # Database Seeders
├── resources/
│   ├── js/
│   │   ├── components/          # React Components
│   │   ├── context/             # React Context
│   │   └── styles/              # CSS Styles
│   └── views/                   # Blade Templates
└── routes/
    ├── api.php                  # API Routes
    └── web.php                  # Web Routes
```

## Development

### Reset Database
```bash
php artisan migrate:fresh --seed
```

### Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
```

### Build for Production
```bash
npm run build
php artisan optimize
```

## Testing Workflow

### Customer Flow
1. Login as customer1@example.com
2. Create new task
3. Select project and category
4. View task (developer hidden)
5. See status updates

### Developer Flow
1. Login as frontend0@example.com
2. View assigned tasks
3. Open task details
4. Update task status
5. See customer information

## Future Enhancements

- [ ] Tester role implementation
- [ ] Task comments and communication
- [ ] File attachments
- [ ] Email notifications
- [ ] Task priority levels
- [ ] Due dates and deadlines
- [ ] Task history tracking
- [ ] Real-time updates
- [ ] Advanced filtering
- [ ] Analytics dashboard

## Security Features

- Laravel Sanctum token authentication
- Password hashing with bcrypt
- CSRF protection
- Role-based middleware
- Input validation
- SQL injection prevention

## License

MIT License

## Support

For detailed information, see:
- [SETUP_GUIDE.md](SETUP_GUIDE.md)
- [DOCUMENTATION.md](DOCUMENTATION.md)
