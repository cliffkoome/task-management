# TaskFlow — Task Management API

A RESTful Task Management API built with plain PHP, MySQL, and Vanilla JS.

---

## Live Demo
🔗 https://task-management-production-616b.up.railway.app

---

## Tech Stack

- **Backend:** PHP 8.2 (no framework, OOP)
- **Database:** MySQL / MariaDB
- **Frontend:** Vanilla JS + Vanilla CSS (separate files)
- **Server:** Apache (Docker container on Railway)

---

## Project Structure
```
task-management/
├── app/
│   ├── controllers/
│   │   └── TaskController.php    ← Business logic
│   └── models/
│       └── Task.php              ← Database queries
├── config/
│   └── database.php              ← DB connection (supports env vars)
├── public/
│   ├── index.php                 ← Frontend HTML
│   ├── css/
│   │   └── style.css             ← Vanilla CSS styles
│   └── js/
│       ├── api.js                ← All fetch/API calls
│       ├── ui.js                 ← DOM helpers & rendering
│       └── app.js                ← Navigation, events & init
├── index.php                     ← Main router
├── api.php                       ← API request handler
├── .htaccess                     ← URL rewriting (local)
├── Dockerfile                    ← Docker deployment config
├── composer.json                 ← PHP dependency declaration
├── task_management.sql           ← Database dump
└── README.md
```

---

## Running Locally

### Requirements
- PHP 8.0+
- MySQL / MariaDB
- Apache with mod_rewrite enabled

### Steps

**1. Clone the repository**
```bash
git clone https://github.com/cliffkoome/task-management.git
```

**2. Copy to your web root**
```bash
# Linux (Manjaro/Arch)
cp -r task-management /srv/http/

# Linux (Ubuntu/Debian)
cp -r task-management /var/www/html/
```

**3. Create the database**
```bash
mysql -u root -p -e "CREATE DATABASE task_management;"
mysql -u root -p task_management < task_management.sql
```

**4. Create a database user**
```sql
CREATE USER 'taskuser'@'localhost' IDENTIFIED BY 'password123';
GRANT ALL PRIVILEGES ON task_management.* TO 'taskuser'@'localhost';
FLUSH PRIVILEGES;
```

**5. Configure database connection**

Edit `config/database.php`:
```php
private $host = 'localhost';
private $db   = 'task_management';
private $user = 'taskuser';
private $pass = 'password123';
```

**6. Enable Apache mod_rewrite**
```bash
# Ubuntu/Debian
sudo a2enmod rewrite
sudo systemctl restart apache2

# Manjaro/Arch — add to /etc/my.cnf.d/server.cnf
skip_ssl
```

**7. Visit the app**
```
http://localhost/task-management/public/index.php
```

> **Note:** When running locally the API base path is `/task-management/api`.
> When deployed the base path is `/api`.

---

## API Endpoints

### Create Task
```
POST /api/tasks
Content-Type: application/json

{
  "title": "Fix login bug",
  "due_date": "2026-04-01",
  "priority": "high"
}
```

**Response — 201 Created:**
```json
{
  "id": 1,
  "title": "Fix login bug",
  "due_date": "2026-04-01",
  "priority": "high",
  "status": "pending",
  "created_at": "2026-04-01 10:00:00",
  "updated_at": "2026-04-01 10:00:00"
}
```

---

### List Tasks
```
GET /api/tasks
GET /api/tasks?status=pending
GET /api/tasks?status=in_progress
GET /api/tasks?status=done
```

Tasks are sorted by priority (high → medium → low) then by due date ascending.

---

### Update Task Status
```
PATCH /api/tasks/{id}/status
Content-Type: application/json

{ "status": "in_progress" }
```

Status can only move forward: `pending → in_progress → done`

---

### Delete Task
```
DELETE /api/tasks/{id}
```

Only tasks with status `done` can be deleted. Returns `403 Forbidden` otherwise.

---

### Daily Report (Bonus)
```
GET /api/tasks/report?date=2026-04-01
```

**Response:**
```json
{
  "date": "2026-04-01",
  "summary": {
    "high":   { "pending": 2, "in_progress": 1, "done": 0 },
    "medium": { "pending": 1, "in_progress": 0, "done": 3 },
    "low":    { "pending": 0, "in_progress": 0, "done": 1 }
  }
}
```

---

## Business Rules

- Task title cannot duplicate on the same due date
- Due date must be today or in the future
- Priority must be: `low`, `medium`, or `high`
- Status can only progress forward: `pending → in_progress → done`
- Cannot skip or revert status
- Only `done` tasks can be deleted

---

## Deployment (Railway)

### Prerequisites
- GitHub account
- Railway account (free tier works)

### Steps

1. Push project to a GitHub repository
2. Go to [railway.app](https://railway.app) and create a new project
3. Click **Deploy from GitHub repo** and select your repository
4. Add a **MySQL** database service
5. Add these environment variables to your app service:

| Variable | Value |
|----------|-------|
| `DB_HOST` | from Railway MySQL `MYSQLHOST` |
| `DB_NAME` | from Railway MySQL `MYSQLDATABASE` |
| `DB_USER` | from Railway MySQL `MYSQLUSER` |
| `DB_PASS` | from Railway MySQL `MYSQLPASSWORD` |
| `DB_PORT` | from Railway MySQL `MYSQLPORT` |

6. Import the database schema:
```bash
mysql --host=MYSQLHOST --port=MYSQLPORT \
      --user=MYSQLUSER --password=MYSQLPASSWORD \
      MYSQLDATABASE < task_management.sql
```

7. Railway auto-detects the `Dockerfile` and deploys
8. Generate a domain under **Settings → Networking → Generate Domain**

### Docker

The project includes a `Dockerfile` using Ubuntu 22.04 + Apache + PHP for reliable cross-platform deployment:
```dockerfile
FROM ubuntu:22.04
...apache2, php, php-mysql, libapache2-mod-php
```

---

## Example API Requests (cURL)
```bash
# Create a task
curl -X POST https://task-management-production-616b.up.railway.app/api/tasks \
  -H "Content-Type: application/json" \
  -d '{"title":"Fix login bug","due_date":"2026-04-01","priority":"high"}'

# List all tasks
curl https://task-management-production-616b.up.railway.app/api/tasks

# Filter by status
curl https://task-management-production-616b.up.railway.app/api/tasks?status=pending

# Update status
curl -X PATCH https://task-management-production-616b.up.railway.app/api/tasks/1/status \
  -H "Content-Type: application/json" \
  -d '{"status":"in_progress"}'

# Delete a done task
curl -X DELETE https://task-management-production-616b.up.railway.app/api/tasks/1

# Daily report
curl "https://task-management-production-616b.up.railway.app/api/tasks/report?date=2026-04-01"
```

---

## Author

**Clifford**
Software Engineering Internship — Coding Challenge 2026
Submitted to: support@cytonn.com
GitHub: https://github.com/cliffkoome/task-management