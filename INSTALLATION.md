# WELDING PORTFOLIO - INSTALLATION GUIDE

## Complete Setup Instructions

### Prerequisites
- PHP 8.1 or higher
- Composer
- MySQL 8.0+
- Node.js 16+ & NPM
- Apache/Nginx web server

---

## STEP-BY-STEP INSTALLATION

### 1. Project Setup

```bash
# Navigate to your web directory
cd /var/www/html  # or your web root

# Clone or extract project
# (If cloning from git)
git clone <repository-url> welding-portfolio
cd welding-portfolio

# Or create directory and copy files
mkdir welding-portfolio
cd welding-portfolio
# Copy all project files here
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### 3. Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

Edit `.env` file with your configuration:

```env
APP_NAME="Professional Welding Services"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=welding_portfolio
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

# Email Configuration (for contact form)
MAIL_MAILER=smtp
MAIL_HOST=smtp.your-email-provider.com
MAIL_PORT=587
MAIL_USERNAME=your_email@domain.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=info@your-domain.com
MAIL_FROM_NAME="${APP_NAME}"

# Business Information
WHATSAPP_NUMBER="+1234567890"
CONTACT_EMAIL="info@your-domain.com"
BUSINESS_NAME="Professional Welding Services"
BUSINESS_ADDRESS="123 Industrial Ave, Your City, ST 12345"
BUSINESS_PHONE="+1 (555) 123-4567"
```

### 4. Database Setup

```bash
# Create database
mysql -u root -p
CREATE DATABASE welding_portfolio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;

# Run migrations
php artisan migrate

# Seed database with sample data
php artisan db:seed
```

### 5. Storage Setup

```bash
# Create symbolic link for storage
php artisan storage:link

# Set permissions (Linux/Mac)
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Or for development
chmod -R 777 storage bootstrap/cache
```

### 6. Build Assets

```bash
# For development
npm run dev

# For production
npm run build
```

### 7. Web Server Configuration

#### Apache (.htaccess)

Create/verify `.htaccess` in public directory:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

Configure Apache virtual host:

```apache
<VirtualHost *:80>
    ServerName your-domain.com
    ServerAlias www.your-domain.com
    DocumentRoot /var/www/html/welding-portfolio/public

    <Directory /var/www/html/welding-portfolio/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/welding-error.log
    CustomLog ${APACHE_LOG_DIR}/welding-access.log combined
</VirtualHost>
```

#### Nginx

```nginx
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
    root /var/www/html/welding-portfolio/public;

    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 8. Restart Web Server

```bash
# Apache
sudo systemctl restart apache2

# Nginx
sudo systemctl restart nginx
sudo systemctl restart php8.1-fpm
```

### 9. Cache Configuration (Production)

```bash
# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Clear cache if needed
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## POST-INSTALLATION CUSTOMIZATION

### 1. Update Business Information

Edit `.env` file:
- BUSINESS_NAME
- BUSINESS_ADDRESS
- BUSINESS_PHONE
- CONTACT_EMAIL
- WHATSAPP_NUMBER

### 2. Add Your Content

#### Using Database Directly

```sql
-- Add Services
INSERT INTO services (title, slug, description, icon, is_featured, is_active) 
VALUES ('Your Service', 'your-service', 'Description', 'fas fa-fire', 1, 1);

-- Add Portfolio Items
INSERT INTO portfolios (title, slug, description, category_id, is_featured, is_active) 
VALUES ('Project Name', 'project-name', 'Description', 1, 1, 1);
```

#### Upload Images

Place images in:
- `public/storage/services/` - Service images
- `public/storage/portfolio/` - Portfolio images
- `public/storage/blog/` - Blog post images

### 3. Configure Social Media Links

Edit `resources/views/partials/footer.blade.php`:
Update social media URLs in the footer section.

### 4. Update Logo and Branding

Edit `resources/views/partials/navbar.blade.php`:
Replace "WeldPro" with your business name or logo.

### 5. Google Maps Integration

Get Google Maps API key from: https://console.cloud.google.com

Add to `.env`:
```env
GOOGLE_MAPS_API_KEY=your_api_key_here
```

Update embed URL in `resources/views/contact.blade.php` with your business location.

---

## MAINTENANCE

### Regular Tasks

```bash
# Clear application cache
php artisan cache:clear

# Clear and recreate route cache
php artisan route:clear
php artisan route:cache

# Clear and recreate config cache
php artisan config:clear
php artisan config:cache

# Clear compiled views
php artisan view:clear
```

### Backup Database

```bash
# Export database
mysqldump -u username -p welding_portfolio > backup_$(date +%Y%m%d).sql

# Import database
mysql -u username -p welding_portfolio < backup_20240101.sql
```

### Update Application

```bash
# Pull latest changes
git pull origin main

# Update dependencies
composer install --no-dev
npm install
npm run build

# Run migrations
php artisan migrate

# Clear and cache
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## TROUBLESHOOTING

### Issue: 500 Internal Server Error
**Solution:**
```bash
chmod -R 775 storage bootstrap/cache
php artisan cache:clear
php artisan config:clear
```

### Issue: Images not displaying
**Solution:**
```bash
php artisan storage:link
chmod -R 775 storage/app/public
```

### Issue: Contact form not sending emails
**Solution:**
- Verify MAIL_* settings in `.env`
- Check `storage/logs/laravel.log` for errors
- Test SMTP credentials

### Issue: CSS/JS not loading
**Solution:**
```bash
npm run build
php artisan cache:clear
```

---

## SECURITY CHECKLIST

- [ ] Set APP_DEBUG=false in production
- [ ] Use strong database passwords
- [ ] Enable HTTPS/SSL certificate
- [ ] Set proper file permissions (755 for directories, 644 for files)
- [ ] Restrict storage and bootstrap/cache permissions
- [ ] Keep Laravel and dependencies updated
- [ ] Use .env file for sensitive data
- [ ] Implement rate limiting on contact form
- [ ] Enable CSRF protection (enabled by default)
- [ ] Regular database backups

---

## PERFORMANCE OPTIMIZATION

```bash
# Enable OPcache (php.ini)
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000

# Use production asset builds
npm run build

# Enable response caching
php artisan route:cache
php artisan config:cache
php artisan view:cache

# Optimize Composer autoloader
composer install --optimize-autoloader --no-dev
```

---

## SUPPORT

For issues or questions:
1. Check Laravel documentation: https://laravel.com/docs/10.x
2. Review application logs: `storage/logs/laravel.log`
3. Enable debug mode temporarily: `APP_DEBUG=true` in `.env`

---

## LIVE DEPLOYMENT CHECKLIST

- [ ] All environment variables configured in `.env`
- [ ] Database created and migrated
- [ ] Storage linked and permissions set
- [ ] Assets built for production (`npm run build`)
- [ ] Caches cleared and regenerated
- [ ] Web server configured correctly
- [ ] SSL certificate installed
- [ ] Email configuration tested
- [ ] Contact form tested
- [ ] All pages loading correctly
- [ ] Mobile responsiveness verified
- [ ] Images uploading and displaying
- [ ] SEO meta tags configured
- [ ] Google Analytics added (optional)
- [ ] Backup system in place
