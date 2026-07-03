# ZO Letters

**Professional Letterhead Management System**
**Version: 1.4.0**

ZO Letters is a Laravel-based application for managing document templates and creating professional letters with customizable letterheads and formatting.

## Quick Installation (Web Installer)

1. Upload all files to your web server
2. Set `public` folder as document root
3. Visit your domain in a browser
4. Follow the installation wizard
5. Login with your admin credentials

That's it! No terminal commands required.

## Features

### Phase 1 - Core Features
- **Template Management**: Create, edit, delete, and view document templates with customizable margins, page sizes, and orientation
- **Document Management**: Create and manage documents with rich HTML content
- **Rich Text Editor**: Integrated TinyMCE editor for document body content
- **Auto Reference Numbers**: Automatically generated sequential reference numbers (ZOI/LTR/YYYY/000001)
- **Dashboard**: Overview with template and document counts, recent documents

### Phase 2 - PDF Generation
- **PDF Generation Engine**: Convert documents to high-quality PDF
- **Page Formats**: A4, A5, Letter, Legal with Portrait/Landscape support
- **Template Integration**: Headers, footers, margins from templates
- **Preview**: In-browser PDF preview before download
- **Download**: Print-ready PDFs with selectable/searchable text
- **Auto-save**: PDFs stored automatically in storage
- **Smart Caching**: Only regenerates PDF when document changes
- **Metadata**: Title, Author, Subject, Keywords, Creator embedded

### Phase 3 - Production Ready
- **Letter Preview**: Professional preview page before PDF generation
- **Print Functionality**: Browser print support with proper CSS
- **Document Status**: Draft, Final, Printed status tracking
- **Advanced Search**: Search by reference, recipient, subject, date
- **Filters**: Filter by status, template, date range
- **Archive/Restore**: Soft delete with restore capability
- **Duplicate Letter**: Create copy with new reference number
- **Dashboard Improvements**: Drafts, recent, quick actions
- **Application Settings**: Company info, reference prefix, defaults
- **Soft Deletes**: Archive instead of permanent delete

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
- PDF status (Ready/Pending)

### PDF Features
- **Preview**: View PDF in browser before downloading
- **Download**: Generate high-quality print-ready PDF
- **Regenerate**: Force regenerate PDF when needed
- **Auto-generation**: PDFs created automatically on document save
- **Smart Caching**: Reuses existing PDF unless content changed
- **Multi-page Support**: Automatic page breaks for long documents
- **Header/Footer**: Template header and footer images included
- **Unicode Support**: Proper character encoding for international text

## Security

- CSRF protection on all forms
- XSS protection via HTML escaping
- SQL injection prevention via prepared statements
- Secure file upload validation
- Session security

## License

Proprietary Software © ZO International. All Rights Reserved.
