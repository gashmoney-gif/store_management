# Store Manager — mobile inventory app (PHP)

A small mobile-friendly web app for tracking store inventory: item code, item name,
storage location, and quantity. Admins can add/edit/delete entries; everyone else
can only log in and view/search.

## Features

- **Login with roles** — `admin` can create, edit, and delete entries. `viewer`
  (and any other non-admin role) can only log in and browse/search — no edit
  controls are even rendered for them.
- **Search** — one search box matches item code, item name, or location at the
  same time (partial match), so staff can search by whichever they know.
- **Mobile-first UI** — single-column card list, big tap targets, sticky header,
  floating "+" button for admins, works well on a phone browser (no app store
  install needed — it's a website you can also "Add to Home Screen").
- Quantity is colour-flagged (green/amber/red) so low or out-of-stock items are
  obvious at a glance.

## Requirements

- PHP 8.0+ with the PDO MySQL extension
- MySQL or MariaDB
- Any web server (Apache/Nginx) or just PHP's built-in server for testing

## Setup

1. **Create the database.** Import `schema.sql` — this creates the database,
   the `users` and `inventory` tables, and two default accounts:
   - `admin / Admin123!` (role: admin)
   - `viewer / Viewer123!` (role: viewer)

   ```bash
   mysql -u root -p < schema.sql
   ```
   (or use phpMyAdmin → Import → choose `schema.sql`)

2. **Set your DB credentials** in `config.php`:

   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'store_management');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```

3. **Run it.**

   - On a phone/local network for testing, PHP's built-in server works:
     ```bash
     php -S 0.0.0.0:8000
     ```
     Then open `http://<your-computer-ip>:8000` from your phone's browser
     (same Wi‑Fi network).
   - For real use, upload the whole folder to any PHP hosting (shared
     hosting, XAMPP/MAMP, a VPS with Apache/Nginx, etc.) and create the
     database there the same way.

4. **Log in and change the default passwords** — open the app, log in as
   `admin`, and update the `users` table (or add a "change password" screen
   if you want one) before giving out real accounts.

## Adding more users

Right now accounts are managed directly in the database. To add a new viewer
or admin:

```sql
INSERT INTO users (username, password_hash, full_name, role)
VALUES ('newuser', '<bcrypt hash>', 'Full Name', 'viewer');
```

Generate a bcrypt hash from the command line:

```bash
php -r "echo password_hash('theirPassword', PASSWORD_BCRYPT), PHP_EOL;"
```

## File overview

```
config.php          Database connection settings
schema.sql           Database schema + default accounts + sample data
login.php / logout.php
index.php            Inventory list + search (all logged-in users)
add_entry.php         Add item (admin only)
edit_entry.php        Edit item (admin only)
delete_entry.php      Delete item (admin only, POST + confirm)
includes/auth.php     Session, role checks, CSRF helpers
includes/header.php, includes/footer.php
assets/css/style.css  Mobile-first styling
```

## Notes on security

- Passwords are hashed with bcrypt (`password_hash`/`password_verify`).
- All database queries use PDO prepared statements.
- Forms that change data include a CSRF token, checked on submit.
- Every admin-only page calls `require_admin()`, which redirects non-admins
  back to the inventory list — so viewers can't reach add/edit/delete pages
  even by typing the URL directly.

Consider adding HTTPS (via your host or a reverse proxy) before using this
with real accounts on the open internet.
