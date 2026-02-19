# Inosakti Website

Website company profile and product pages built with native PHP (no framework), designed to run on XAMPP for local development and cPanel hosting for production.

## Public Scope

This repository is for source code and non-sensitive assets only.

- Do not commit credentials, API keys, or server access details.
- Keep private operations notes in `README.private.md` (ignored by Git).
- Keep environment values in `.env` and never commit real secrets.

## Tech Stack

- PHP
- Apache (`.htaccess` rewrite rules)
- MySQL/MariaDB

## Project Structure

- `index.php`: homepage entry point
- `inc/config.php`: environment loader, DB config, base path logic
- `inc/header.php`, `inc/footer.php`: shared layout components
- `admin/`: admin panel (login, dashboard, posts, courses, orders, users)
- `pages/`: website pages (services, products, company, blog, learning)
- `assets/`: static files (images, icons, etc.)

## Admin Panel

Access:

- `/admin/login` for sign in
- `/admin/` for dashboard after login

Current admin modules:

- Dashboard KPI (`posts`, `courses`, `orders`, `users`, `employees`, `enrollments`)
- Blog post status management
- Course status management
- Product catalog management (`admin/products`)
- Order status management
- User active/inactive management

Login requirements:

- `users` table must contain at least one active user
- `password_hash` must be generated using PHP `password_hash()`

Commerce catalog notes:

- `pages/products/shop.php` now renders products from database (`products`) instead of hardcoded cards.
- Run migration `database/010_catalog_products_cms.sql` to add:
  - `product_categories` table
  - `products.category_id`
  - `products.image_path`
  - initial seed data for shop products

## Environment Configuration

Required environment keys:

```env
DB_HOST=localhost
DB_PORT=3306
DB_NAME=your_database
DB_USER=your_username
DB_PASS=your_password
```

Environment file lookup order in `inc/config.php`:

1. Production: `/home/<cpanel_user>/environment/.env`
2. Local fallback: `<project_root>/.env`

## Local Development (XAMPP)

1. Place project in `htdocs` (example: `C:\Program Data\XAMPP\htdocs\inosakti.com`).
2. Create local `.env` in project root.
3. Start Apache and MySQL from XAMPP.
4. Open:
   - `http://localhost/inosakti.com` (subfolder mode), or
   - your virtual host URL if configured.

## Production Deployment (cPanel)

1. Put app in `public_html` (or domain document root).
2. Put `.env` outside web root, for example:
   - `/home/<cpanel_user>/environment/.env`
3. Set `.env` permission to `600` or `640`.
4. Keep `.htaccess` active to preserve rewrite/security rules.

## Security Notes

- Never upload `.env` to public repositories.
- Rotate credentials if secrets are accidentally exposed.
- Review `.gitignore` periodically to keep new sensitive files out of Git.

## Maintenance

- Update this public README when architecture, run steps, or deployment flow changes.
- Keep private procedures and credentials in `README.private.md` only.
