# ZO Letters

**Professional Document Automation & Mail Merge Platform**

ZO Letters is a Laravel-based application for managing document templates and creating professional letters with customizable letterheads and formatting.

## Phase 1 Features

- **Template Management**: Create, edit, delete, and view document templates with customizable margins, page sizes, and orientation
- **Document Management**: Create and manage documents with rich HTML content
- **Rich Text Editor**: Integrated TinyMCE editor for document body content
- **Auto Reference Numbers**: Automatically generated sequential reference numbers (ZOI/LTR/YYYY/000001)
- **Dashboard**: Overview with template and document counts, recent documents

## Requirements

- PHP 8.2+
- MySQL/MariaDB
- Composer
- Apache (mod_rewrite enabled)

## Installation

1. Clone the repository:
```bash
git clone https://github.com/zointernational/zoletters.git
cd zoletters
```

2. Install dependencies:
```bash
composer install
```

3. Copy environment file:
```bash
cp .env.example .env
```

4. Generate application key:
```bash
php artisan key:generate
```

5. Create database and update `.env`:
```env
DB_DATABASE=zoletters
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

6. Run migrations:
```bash
php artisan migrate
```

7. Set storage permissions:
```bash
chmod -R 775 storage bootstrap/cache
chmod -R 775 public/uploads
```

8. Create symbolic link for uploads:
```bash
ln -s /path/to/public/uploads /workspace/project/zoletters/public/uploads
```

## Web Server Configuration

### Apache (Shared Hosting)

Create `.htaccess` in public directory if not present:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.+)$ index.php/$1 [L]
</IfModule>
```

## Usage

Navigate to your domain to access the application.

### Navigation
- **Dashboard**: Main overview page
- **Templates**: Manage document templates
- **Documents**: Create and manage documents

### Template Fields
- Name and description
- Header and footer images (PNG, JPEG, WEBP - max 5MB)
- Page size (A4, A5, Letter, Legal)
- Orientation (Portrait, Landscape)
- Margins (top, bottom, left, right in mm)
- Status (Active, Inactive)

### Document Fields
- Reference Number (auto-generated)
- Template selection
- Recipient name and address
- Subject
- Body (rich HTML content)

## Security

- CSRF protection on all forms
- XSS protection via HTML escaping
- SQL injection prevention via prepared statements
- Secure file upload validation
- Session security

## License

Proprietary Software © ZO International. All Rights Reserved.
