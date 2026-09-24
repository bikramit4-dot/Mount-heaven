# 🏔️ Mount Heaven English School — Website

A complete, secure school website built with **plain PHP 8 (MVC pattern)** and **MySQL** — no framework needed.

Every piece of content on the website is managed from the built-in **Admin Panel** at `/admin`.

---

## ✨ Features

### Public website
- **Home** — animated hero slider, highlights strip, about preview, programs, facilities, notices & events, admission CTA
- **About** — story, mission & vision, principal's message, faculty grid
- **Academics** — programs timeline and campus facilities
- **Admissions** — open/closed status pill, how-to-apply steps, documents checklist
- **Gallery** — albums with filter buttons and a photo lightbox
- **Notices & Events** — pinned notices and dated events
- **Contact** — info cards + message form (saved to admin inbox)

### Admin Panel (`/admin`)
- 📊 Dashboard with stats and quick actions
- ⚙️ **Site Settings** — every text, phone, email, social link, admission toggle, principal info & photo
- 🖼️ Home Sliders — add/edit/delete with image upload
- 📢 Notices, 📅 Events, 📚 Programs, 🏫 Facilities, 👩‍🏫 Teachers — full CRUD
- 📷 Gallery — albums + multi-photo upload
- 💬 Messages inbox — read/unread/delete, unread badge everywhere
- 👥 Users (admin-only) — create editors/admins, delete users
- 🔑 My Profile — change name, email and password
- 💾 **Backup & ♻️ Restore tabs** — one-click full database backup, download, restore from saved file or upload, delete

---

## 🚀 Quick Start (PHP built-in server)

```bash
# 1. Start MySQL and configure the environment:
cp .env.example .env
# Edit .env with your database credentials and a unique admin password.

# The app loads .env automatically. Shell environment variables still take
# precedence, so these exports are also supported:
export DB_HOST=127.0.0.1
export DB_NAME=mount_heaven_school
export DB_USER=root
export DB_PASS=yourpassword

# 2. Run the app (from the project root)
php -S localhost:8000 server.php

# 3. Open the site
#    Website : http://localhost:8000
#    Admin   : http://localhost:8000/admin
```

**First run automatically creates the database and seeds all demo content.**

### Default admin login

| Username | Password   |
|----------|------------|
| `admin`  | `Admin@123` |

> ⚠️ Change this password immediately (Admin → My Profile).

### Run with Apache/Nginx
Point the document root at the **`public/` folder** (recommended — Apache `.htaccess`
files are included) or at the project root. Both work.
Make sure `storage/backups` is writable by PHP.

### 📁 Moving the site to a sub-folder or another host
All URLs, redirects and uploaded-image links are built through the central helpers
`app_base()` / `public_base()` / `url()` / `asset()` / `upload_url()` — you never hard-code
paths. In almost every case **zero configuration is needed** when you move the project:

| Deployment | What happens |
|---|---|
| `http://host/` (docroot = repo root) | auto-detected base `""` — works |
| `http://host/school/` (docroot = repo root) | auto-detected base `/school` — works |
| docroot = `public/` directly | base `""`, assets at root — works |
| Shared host where detection fails | set `APP_BASE_URL=/school` in `.env` |

`APP_BASE_URL` in `.env` is the manual override (like `BASE_URL` in the wellness project):
set it to the sub-folder path (`/school`) or `/` for the domain root and every link,
redirect and image URL follows it — no find-and-replace needed.

---

## 🔐 Security Features

- **MVC architecture** — Router → Controller → Model (Database) → View, fully separated
- **Password hashing** — bcrypt via `password_hash()` with automatic rehash
- **CSRF protection** — token on every POST form, verified with `hash_equals`
- **SQL injection safe** — 100% prepared statements
- **XSS safe** — all output escaped through `e()` helper
- **Brute-force protection** — login rate limiting (5 tries / 10 min per IP+user)
- **Session hardening** — HttpOnly, SameSite cookies, periodic ID rotation
- **Secure uploads** — extension + MIME + `getimagesize` checks, random filenames
- **Path-traversal protection** — backup download and file deletes are sanitized
- **Admin-only areas** — every admin controller checks authentication; user management is admin-role-only
- **Security headers** — nosniff, frame-options, referrer-policy, HSTS on HTTPS
- **.htaccess guards** — `app/`, `core/`, `config/`, `database/`, `routes/`, `storage/` blocked from the web (root rules); own `.htaccess` in `public/` for docroot=public installs; PHP execution disabled in uploads
- **Honeypot spam trap** on the public contact form

---

## 🗂️ Project Structure (MVC)

```
├── index.php               # thin shim → public/index.php (keeps root-docroot installs working)
├── database/
│   ├── database.sql         # schema and seed data (used by auto-installer)
│   └── migrations/          # incremental schema changes applied on boot
├── app/
│   ├── controllers/         # Controllers (public + admin/)
│   ├── models/              # Models — all SQL for the main entities lives here
│   └── views/
│       ├── layouts/        # public, admin, admin-auth layouts
│       ├── pages/          # public website pages
│       └── admin/          # admin panel pages
├── core/                    # Router, Database, Auth, CSRF, Session, Validator, Uploader, Model
├── config/config.php        # application and database configuration
├── routes/web.php           # all routes
├── public/                 # WEB ROOT — the only web-accessible folder
│   ├── index.php            # front controller (bootstraps the app)
│   ├── css/
│   ├── js/
│   ├── uploads/
│   └── .htaccess
├── storage/backups/        # database backups (web-blocked)
└── server.php              # dev server router (serves public/ only)
```

### 🧭 Path/URL conventions (the one rule)
`public/index.php` → `Router` (strips the sub-folder base) → `Controller` → `Model` → `Database` → view.
Every URL in views and redirects goes through a helper — never write raw `/css/...`,
`/uploads/...` or `/admin/...` paths:

| Helper | Use for | Example output |
|---|---|---|
| `url('/admin/sliders')` | internal page/route links | `/school/admin/sliders` |
| `asset('css/style.css')` | CSS/JS (adds `?v=` cache-buster) | `/school/public/css/style.css?v=...` |
| `upload_url($row['image'])` | uploaded images from the DB | `/school/public/uploads/sliders/x.jpg` |
| `$this->redirect('/admin')` | controller redirects (base added automatically) | → `/school/admin` |

---

## 💾 Backup & Restore Notes

- **Backup** creates a full `.sql` dump (structure + data) into `storage/backups` and lets you download it.
- **Restore** drops and rebuilds all tables from the chosen file inside a transaction — either fully succeeds or changes nothing.
- Uploaded **images are not in the SQL file** — copy the `public/uploads/` folder separately for a complete site backup.
