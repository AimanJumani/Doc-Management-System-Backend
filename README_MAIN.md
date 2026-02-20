# Document Management System

A full-stack document management system built for ABC Corporation where employees can access company documents based on their role and department. The system enables managers to upload documents, employees to search and download them, and administrators to manage the entire system.

## Features

- **User Authentication & Authorization** – Registration, login/logout with JWT tokens (Laravel Sanctum)
- **Role-Based Access Control (RBAC)** – Admin, Manager, Employee roles with granular permissions
- **Document Management** – Upload, view, edit, delete, and download documents
- **Search & Filter** – Search by title/description, filter by category, department, and access level
- **File Validation** – Supports PDF, DOCX, XLSX, JPG, PNG (max 10MB)
- **Responsive UI** – Mobile, tablet, and desktop layouts with Tailwind CSS
- **Pagination & Sorting** – 20 documents per page with sort by name, date, size, downloads
- **Dashboard** – Stats overview, quick actions, and recent documents

## Tech Stack

### Backend
- Laravel 12 (PHP 8.2+)
- SQLite (configurable to PostgreSQL)
- Laravel Sanctum (token-based authentication)
- Spatie Laravel Permission (role management)
- Laravel Storage (file management)
- API Resources for JSON responses

### Frontend
- React 19 with Vite
- React Router v7 for navigation
- Axios for HTTP requests
- Tailwind CSS v4 for styling

## Setup & Installation

### Prerequisites
- PHP 8.2+
- Composer
- Node.js 18+
- npm

### Backend Setup

```bash
cd backend

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations and seed database
php artisan migrate:fresh --seed

# Create storage link
php artisan storage:link

# Start the server
php artisan serve
```

The backend runs at `http://localhost:8000`

### Frontend Setup

```bash
cd frontend

# Install dependencies
npm install

# Copy environment file
cp .env.example .env

# Start development server
npm run dev
```

The frontend runs at `http://localhost:5173`

## Test Credentials

All passwords are: `password`

| Role | Email | Department |
|------|-------|------------|
| **Admin** | admin@example.com | Information Technology |
| **Manager (HR)** | hr.manager@example.com | Human Resources |
| **Manager (Finance)** | finance.manager@example.com | Finance |
| **Manager (IT)** | it.manager@example.com | Information Technology |
| **Manager (Marketing)** | marketing.manager@example.com | Marketing |
| **Employee** | john.smith@example.com | Human Resources |
| **Employee** | jane.doe@example.com | Human Resources |
| **Employee** | bob.wilson@example.com | Finance |
| **Employee** | alice.brown@example.com | Information Technology |
| **Employee** | charlie.davis@example.com | Marketing |
| **Employee** | diana.evans@example.com | Operations |
| **Employee** | edward.clark@example.com | Operations |

## Role Permissions

| Permission | Admin | Manager | Employee |
|-----------|-------|---------|----------|
| View all documents | ✅ | ❌ | ❌ |
| View public documents | ✅ | ✅ | ✅ |
| View department documents | ✅ | ✅ (own dept) | ✅ (own dept) |
| Upload documents | ✅ (any dept) | ✅ (own dept) | ❌ |
| Edit documents | ✅ (any) | ✅ (own uploads) | ❌ |
| Delete documents | ✅ (any) | ✅ (own uploads) | ❌ |
| Download documents | ✅ | ✅ | ✅ (authorized) |

## API Endpoints

### Authentication
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/v1/register` | Register new user |
| POST | `/api/v1/login` | Login |
| POST | `/api/v1/logout` | Logout (auth required) |
| GET | `/api/v1/user` | Get current user (auth required) |

### Documents
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/documents` | List documents (with search & filter params) |
| GET | `/api/v1/documents/{id}` | Get document details |
| POST | `/api/v1/documents` | Upload document (admin/manager) |
| PATCH | `/api/v1/documents/{id}` | Update document metadata |
| DELETE | `/api/v1/documents/{id}` | Delete document |
| GET | `/api/v1/documents/{id}/download` | Download document |

### Master Data
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/departments` | List departments |
| GET | `/api/v1/categories` | List categories |

### Query Parameters for Document Listing
| Parameter | Type | Description |
|-----------|------|-------------|
| `search` | string | Search by title or description |
| `category_id` | integer | Filter by category |
| `department_id` | integer | Filter by department |
| `access_level` | string | Filter by access level (public/department/private) |
| `sort_by` | string | Sort by: title, created_at, file_size, download_count |
| `sort_order` | string | asc or desc |
| `page` | integer | Page number |
| `per_page` | integer | Items per page (default: 20, max: 100) |

## Running Tests

```bash
cd backend
php artisan test
```

### Test Coverage
1. User registration
2. User login
3. User logout
4. Manager can upload document
5. Employee cannot upload document
6. User can search documents
7. User can filter documents by category
8. Admin can delete any document
9. Employee cannot view private documents
10. Manager cannot upload to other department

## Database Schema

### Departments
- id, name, timestamps

### Categories
- id, title, description, timestamps

### Users
- id, name, email, password, department_id, timestamps

### Documents
- id, title, description, file_name, file_path, file_type, file_size, category_id, department_id, uploaded_by, access_level, download_count, timestamps

## Seed Data
- **5 Departments**: Human Resources, Finance, Information Technology, Marketing, Operations
- **6 Categories**: Policy, Report, Template, Guide, Form, Other
- **12 Users**: 1 Admin, 4 Managers, 7 Employees
- **30 Documents**: 50% public, 30% department, 20% private
