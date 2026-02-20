# Document Management System — Setup & Run Guide

> **ABC Corporation — Full Stack Document Management System**
> Laravel 12 (Backend) + React 19 (Frontend)

---

## 📋 Prerequisites

Before starting, make sure you have these installed on your computer:

| Software    | Minimum Version | Download Link                                      |
|-------------|-----------------|-----------------------------------------------------|
| **PHP**     | 8.2 or higher   | https://windows.php.net/download/ or https://www.apachefriends.org/ (XAMPP) |
| **Composer**| 2.x             | https://getcomposer.org/download/                   |
| **Node.js** | 18 or higher    | https://nodejs.org/                                 |
| **npm**     | 9 or higher     | (comes with Node.js)                                |

### How to check if they are installed

Open **Command Prompt** or **PowerShell** and run:

```
php -v
composer -v
node -v
npm -v
```

Each should print a version number. If any shows an error, install it from the link above.

### Required PHP Extensions

Make sure these PHP extensions are enabled in your `php.ini` file:
- `pdo_sqlite`
- `mbstring`
- `openssl`
- `tokenizer`
- `fileinfo`

> **XAMPP Users:** XAMPP usually has all these enabled by default. If not, open `C:\xampp\php\php.ini` and remove the `;` before the extension line.

---

## 📦 Step 1: Extract the ZIP File

1. Right-click the ZIP file → **Extract All** (or use WinRAR/7-Zip)
2. Extract to a location of your choice, for example: `D:\document management system`
3. After extraction, you should see this folder structure:

```
document management system/
├── backend/          ← Laravel API (PHP)
├── frontend/         ← React App (JavaScript)
├── README.md         ← Technical documentation
└── SETUP_GUIDE.md    ← This file
```

---

## ⚙️ Step 2: Setup the Backend (Laravel API)

Open **Command Prompt** or **PowerShell** and run these commands one by one:

### 2.1 — Navigate to the backend folder

```bash
cd "D:\document management system\backend"
```

> Replace the path with wherever you extracted the ZIP file.

### 2.2 — Install PHP dependencies

```bash
composer install
```

> This will download all required Laravel packages. Wait until it finishes (may take 1-3 minutes).

### 2.3 — Setup the environment file

```bash
copy .env.example .env
```

> **Important:** If `.env` already exists in the backend folder, skip this step. The `.env` file is already configured for SQLite (no external database needed).

### 2.4 — Generate the application key

```bash
php artisan key:generate
```

### 2.5 — Create the database and load sample data

```bash
php artisan migrate:fresh --seed
```

> This creates the SQLite database with:
> - 5 Departments (HR, Finance, IT, Marketing, Operations)
> - 6 Document Categories (Policy, Report, Template, Guide, Form, Other)
> - 3 Roles (Admin, Manager, Employee)
> - 12 Users (1 Admin + 4 Managers + 7 Employees)
> - 30 Sample Documents with dummy files

### 2.6 — Start the backend server

```bash
php artisan serve --port=8000
```

You should see:
```
INFO  Server running on [http://127.0.0.1:8000].
```

> **Leave this terminal window open!** The backend server must keep running.

---

## 🖥️ Step 3: Setup the Frontend (React App)

Open a **NEW** Command Prompt or PowerShell window (keep the backend terminal running) and run:

### 3.1 — Navigate to the frontend folder

```bash
cd "D:\document management system\frontend"
```

### 3.2 — Install JavaScript dependencies

```bash
npm install
```

> This will download all React packages. Wait until it finishes (may take 1-3 minutes).

### 3.3 — Setup the environment file

```bash
copy .env.example .env
```

> **Important:** If `.env` already exists in the frontend folder, skip this step.

### 3.4 — Start the frontend server

```bash
npm run dev
```

You should see:
```
VITE v7.x.x  ready in xxx ms

➜  Local:   http://localhost:5173/
```

> **Leave this terminal window open too!** The frontend server must keep running.

---

## 🚀 Step 4: Open the Application

1. Open your web browser (Chrome, Edge, Firefox, etc.)
2. Go to: **http://localhost:5173**
3. You'll see the **Login Page**

---

## 🔐 Step 5: Login with Test Accounts

All passwords are: **`password`**

### Admin Account (Full Access)
| Field    | Value              |
|----------|--------------------|
| Email    | admin@example.com  |
| Password | password           |

### Manager Accounts
| Department       | Email                          | Password |
|------------------|--------------------------------|----------|
| Human Resources  | hr.manager@example.com         | password |
| Finance          | finance.manager@example.com    | password |
| IT               | it.manager@example.com         | password |
| Marketing        | marketing.manager@example.com  | password |

### Employee Accounts
| Name           | Email                     | Department       | Password |
|----------------|---------------------------|------------------|----------|
| John Smith     | john.smith@example.com    | Human Resources  | password |
| Jane Doe       | jane.doe@example.com      | Human Resources  | password |
| Bob Wilson     | bob.wilson@example.com    | Finance          | password |
| Alice Brown    | alice.brown@example.com   | IT               | password |
| Charlie Davis  | charlie.davis@example.com | Marketing        | password |
| Diana Evans    | diana.evans@example.com   | Operations       | password |
| Edward Clark   | edward.clark@example.com  | Operations       | password |

---

## 🧪 Step 6: Test the Features

### As Admin (admin@example.com):
1. **Dashboard** — See total document count (30), department docs, your uploads, and recent documents
2. **Documents** — Browse all 30 documents with search, filter, and sort
3. **Upload** — Click "Upload Document" → fill form → choose any department → upload a file
4. **View** — Click any document title to see full details
5. **Download** — Click "Download" on a document detail page
6. **Edit** — Click "Edit" to update document metadata
7. **Delete** — Click "Delete" → confirm → document is removed

### As Manager (hr.manager@example.com):
1. **Dashboard** — See fewer documents (only public + own department)
2. **Documents** — Cannot see private documents from other departments
3. **Upload** — Can upload, but department is locked to "Human Resources"
4. **Edit/Delete** — Can only edit/delete documents you uploaded

### As Employee (john.smith@example.com):
1. **Dashboard** — See only accessible documents
2. **Documents** — See public docs + department-level docs from own department (NO private docs)
3. **Upload** — Button is hidden; employees cannot upload
4. **Edit/Delete** — Buttons are hidden; employees cannot modify documents

### Register a New User:
1. Click "Logout" → click "Don't have an account? Register"
2. Fill in name, email, password, select a department
3. New users are automatically assigned the **Employee** role

---

## 🧪 Step 7: Run Automated Tests (Optional)

Open a **new terminal** in the backend folder:

```bash
cd "D:\document management system\backend"
php artisan test
```

Expected output: **10 tests passed, 36 assertions**

---

## ⚠️ Troubleshooting

### "php is not recognized as a command"
**Fix:** PHP is not in your system PATH.
- **XAMPP Users:** Run this first in PowerShell:
  ```
  $env:Path = "C:\xampp\php;" + $env:Path
  ```
- Or add PHP to your system PATH permanently: System Properties → Environment Variables → PATH → add `C:\xampp\php`

### "composer is not recognized"
**Fix:** Install Composer from https://getcomposer.org/download/

### "npm is not recognized"
**Fix:** Install Node.js from https://nodejs.org/ (npm comes with it)

### Backend shows "500 Server Error"
**Fix:** Run these commands in the backend folder:
```bash
php artisan key:generate
php artisan migrate:fresh --seed
```

### Frontend shows blank page or network errors
**Fix:** Make sure:
1. The backend server is running on port 8000 (check the first terminal)
2. The `.env` file in the frontend folder has: `VITE_API_URL=http://localhost:8000/api/v1`

### "SQLSTATE: no such table" error
**Fix:** Run the migration command again:
```bash
cd backend
php artisan migrate:fresh --seed
```

### Port 8000 is already in use
**Fix:** Use a different port:
```bash
php artisan serve --port=8001
```
Then update the frontend `.env` file:
```
VITE_API_URL=http://localhost:8001/api/v1
```
And restart the frontend: `npm run dev`

---

## 🛑 How to Stop the Servers

- Press **Ctrl + C** in each terminal window to stop the servers
- To restart, repeat Step 2.6 (backend) and Step 3.4 (frontend)

---

## 📁 Quick Reference

| Item              | URL / Location                            |
|-------------------|-------------------------------------------|
| Frontend App      | http://localhost:5173                      |
| Backend API       | http://localhost:8000/api/v1              |
| Backend Folder    | `backend/`                                |
| Frontend Folder   | `frontend/`                               |
| Database          | `backend/database/database.sqlite`        |
| Uploaded Files    | `backend/storage/app/private/documents/`  |
| API Documentation | See `README.md`                           |
