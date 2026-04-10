# ERP Reports Portal — Laravel 10

A complete ERP reporting portal built with **Laravel 10**, **PHP 8.1+**, **Bootstrap 5**, and **DomPDF**.

---

## Features

- ✅ Secure login (email or username, remember me, bcrypt passwords)
- ✅ Role-based access control (Admin, Manager, Accountant, Sales, Viewer)
- ✅ Dynamic sidebar navigation — auto-builds from report categories in the database
- ✅ Per-role report permissions (manage which roles can see which reports)
- ✅ Dynamic report parameter forms (text, date, select, company picker, etc.)
- ✅ SQL query builder — write raw SQL per report, use `:param` bindings
- ✅ Multi-company support — `{company_prefix}` placeholder maps to your ERP prefix (0_, 1_, etc.)
- ✅ HTML preview + PDF download via DomPDF
- ✅ Audit log of every report run (user, params, IP, time)
- ✅ Full admin panel: Users, Roles, Categories, Reports
- ✅ Admin dashboard with stats and recent activity

---

## Requirements

- PHP 8.1 or 8.2
- Composer
- MySQL / MariaDB
- Your existing FrontAccounting ERP database

---

## Installation

### 1. Download and install

```bash
# Copy project to your server
cd /var/www/html
# (upload or git clone the project here)

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate app key
php artisan key:generate
```

### 2. Configure database

Edit `.env`:

```env
APP_NAME="ERP Reports Portal"
APP_URL=http://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=maubxcuq_erp    # ← your existing ERP database
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
```

### 3. Run migrations and seed

```bash
# This creates the portal's own tables (users, roles, reports, etc.)
# alongside your existing ERP tables in the same database
php artisan migrate --seed
```

### 4. Set permissions

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 5. Configure web server

**Apache** — point DocumentRoot to `/public`

**Nginx:**
```nginx
root /var/www/html/erp-reports/public;
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

### 6. Access the portal

| URL | Credentials |
|-----|-------------|
| `http://your-domain/` | admin@erp.local / Admin@123 |
| `http://your-domain/` | manager@erp.local / Manager@123 |

---

## Default Login Credentials

| User | Email | Password | Role |
|------|-------|----------|------|
| System Administrator | admin@erp.local | Admin@123 | Admin |
| Demo Manager | manager@erp.local | Manager@123 | Manager |

**Change these passwords after first login!**

---

## How to Add a New Report (Admin)

1. Log in as admin
2. Go to **Admin → Report Categories** → create a category if needed
3. Go to **Admin → Manage Reports → Add Report**
4. Fill in:
    - **Name** (e.g. "Monthly Sales")
    - **Category** (e.g. "Sales & Revenue")
    - **SQL Query** — write your SELECT statement. Use:
        - `{company_prefix}` → replaced with the selected company (e.g. `0` or `1`)
        - `:param_name` → replaced with parameter values
5. Add **Parameters** (click "Add Parameter"):
    - `company` type → auto-detects available ERP companies
    - `date` type → shows a date picker
    - `select` with SQL source → populates dropdown from your ERP data
6. Set **Role Access** — tick which roles can run this report
7. Save — the report immediately appears in the sidebar navigation

---

## SQL Query Tips

```sql
-- Basic pattern
SELECT
    col1 AS `Label 1`,
    col2 AS `Label 2`
FROM `{company_prefix}_table_name`
WHERE date_column BETWEEN :date_from AND :date_to
ORDER BY date_column DESC

-- With customer dropdown (configure parameter as select/sql type)
WHERE debtor_no = :customer_id

-- With optional filter
WHERE (:show_inactive = 1 OR inactive = 0)
```

---

## Project Structure

```
app/
  Http/
    Controllers/
      Auth/LoginController.php          — login/logout
      DashboardController.php           — user dashboard
      ReportController.php              — run/preview/generate PDF
      Admin/AdminControllers.php        — all admin controllers
    Middleware/AdminMiddleware.php
  Models/
    User.php, Role.php
    Report.php, ReportCategory.php
    ReportParameter.php, ReportLog.php
  Services/ReportService.php            — SQL execution + PDF generation

resources/views/
  layouts/app.blade.php                 — main layout with dynamic sidebar
  auth/login.blade.php
  dashboard.blade.php
  reports/
    index.blade.php                     — report listing
    show.blade.php                      — parameter form
    preview.blade.php                   — HTML table preview
    pdf-default.blade.php               — PDF template
    partials/parameter-field.blade.php  — dynamic field renderer
  admin/
    dashboard.blade.php
    users/index.blade.php
    roles/index.blade.php
    categories/index.blade.php
    reports/form.blade.php              — report builder with param builder

database/migrations/                    — 3 migration files
database/seeders/DatabaseSeeder.php     — roles, users, sample reports

routes/web.php                          — all routes
```

---

## Customizing the PDF Template

The default PDF template is `resources/views/reports/pdf-default.blade.php`.

To use a custom template for a specific report:
1. Create `resources/views/reports/custom/my-report.blade.php`
2. In the report's admin settings, set **Blade View** to `reports.custom.my-report`

Variables available in PDF templates:
- `$report` — the Report model
- `$rows` — array of result rows
- `$columns` — array of column names
- `$params` — submitted parameters
- `$generatedAt` — Carbon timestamp
- `$generatedBy` — user name string

---

## Security Notes

1. This portal does **not** expose the ERP data editing — read-only SQL SELECT only
2. All SQL queries run as the DB user configured in `.env` — use a read-only DB user for safety:
   ```sql
   CREATE USER 'erp_reports'@'localhost' IDENTIFIED BY 'strong_password';
   GRANT SELECT ON maubxcuq_erp.* TO 'erp_reports'@'localhost';
   ```
3. Change default passwords immediately after installation
4. Consider adding HTTPS via Let's Encrypt

---

## Troubleshooting

| Issue | Fix |
|-------|-----|
| "Undefined variable $roles" in user modal | Ensure you're using the seeded roles |
| PDF blank | Check `storage/logs/laravel.log` for SQL errors |
| Company dropdown empty | Ensure DB connection points to your ERP database |
| 403 on admin pages | User must have role with slug `admin` |
| `{company_prefix}` not replaced | Ensure report has a `company` type parameter |
