# TaskFlow — Task Management API

A RESTful Task Management API built with plain PHP, MySQL, and Vanilla JS.

---

## Tech Stack

- **Backend:** PHP (no framework)
- **Database:** MySQL / MariaDB
- **Frontend:** Vanilla JS, Vanilla CSS
- **Server:** Apache

---

## Project Structure
```
task-management/
├── app/
│   ├── controllers/
│   │   └── TaskController.php
│   └── models/
│       └── Task.php
├── config/
│   └── database.php
├── public/
│   ├── index.php
│   ├── css/
│   │   └── style.css
│   └── js/
│       ├── api.js
│       ├── ui.js
│       └── app.js
├── index.php
├── .htaccess
├── task_management.sql
└── README.md
```

---

## Running Locally

### Requirements
- PHP 8.0+
- MySQL / MariaDB
- Apache with mod_rewrite enabled

### Steps

**1. Clone or extract the project into your web root**
```bash
# Linux (Manjaro/Arch)
cp -r task-management /srv/http/

# Linux (Ubuntu/Debian)
cp -r task-management /var/www/html/
```

**2. Import the database**
```bash
mysql -u root -p -e "CREATE DATABASE task_management;"
mysql -u root -p task_management < task_management.sql
```

**3. Create a database user**
```sql
CREATE USER 'taskuser'@'localhost' IDENTIFIED BY 'password123';
GRANT ALL PRIVILEGES ON task_management.* TO 'taskuser'@'localhost';
FLUSH PRIVILEGES;
```

**4. Configure the database connection**

Edit `config/database.php`:
```php
private $host = 'localhost';
private $db   = 'task_management';
private $user = 'taskuser';
private $pass = 'password123';
```

**5. Enable Apache mod_rewrite**
```bash
sudo a2enmod rewrite       # Ubuntu
sudo systemctl restart apache2
```

**6. Visit the app**
```
http://localhost/task-management/public/index.php
```

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

### List Tasks
```
GET /api/tasks
GET /api/tasks?status=pending
```

### Update Task Status
```
PATCH /api/tasks/{id}/status
Content-Type: application/json

{ "status": "in_progress" }
```
Status can only move: `pending → in_progress → done`

### Delete Task
```
DELETE /api/tasks/{id}
```
Only tasks with status `done` can be deleted.

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
- Only `done` tasks can be deleted

---

## Deployment (Railway)

1. Push project to a GitHub repository
2. Go to [railway.app](https://railway.app) and create a new project
3. Add a **MySQL** plugin
4. Set environment variables:
   - `DB_HOST` — from Railway MySQL settings
   - `DB_NAME` — `task_management`
   - `DB_USER` — from Railway MySQL settings
   - `DB_PASS` — from Railway MySQL settings
5. Update `config/database.php` to use environment variables:
```php
private $host;
private $db;
private $user;
private $pass;

public function __construct() {
    $this->host = getenv('DB_HOST');
    $this->db   = getenv('DB_NAME');
    $this->user = getenv('DB_USER');
    $this->pass = getenv('DB_PASS');
}
```
6. Import `task_management.sql` via Railway's MySQL console
7. Your live URL will be provided by Railway

---

## Author

**Clifford**
Software Engineering Internship — Coding Challenge 2026
Submitted to: support@cytonn.com