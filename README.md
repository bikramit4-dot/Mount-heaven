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
# 1. Start MySQL and set credentials (edit config/config.php or use env vars):
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
Point the document root at the project folder (Apache `.htaccess` files are included).
Make sure `storage/backups` is writable by PHP.

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
- **.htaccess guards** — `app/`, `config/`, `database/`, `storage/` blocked from the web; PHP execution disabled in uploads
- **Honeypot spam trap** on the public contact form

---

## 🗂️ Project Structure (MVC)

```
├── app/
│   ├── Controllers/        # Controllers (public + Admin/)
│   ├── Core/               # Router, Database, Auth, CSRF, Session, Validator, Uploader
│   ├── Views/
│   │   ├── layouts/        # public, admin, admin-auth layouts
│   │   ├── public/         # website pages
│   │   └── admin/          # admin panel pages
│   └── routes.php          # all routes
├── config/config.php       # app + database configuration
├── database/schema.sql     # tables + seed content
├── public/                 # web root: index.php, assets, uploads
├── storage/backups/        # database backups (web-blocked)
└── server.php              # dev server router
```

---

## 💾 Backup & Restore Notes

- **Backup** creates a full `.sql` dump (structure + data) into `storage/backups` and lets you download it.
- **Restore** drops and rebuilds all tables from the chosen file inside a transaction — either fully succeeds or changes nothing.
- Uploaded **images are not in the SQL file** — copy the `public/uploads/` folder separately for a complete site backup.
