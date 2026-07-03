# Changelog

All notable changes to this project will be documented in this file.

## [1.3.0] - 2025-07-03

### Added
- **Professional Web Installer**: Complete step-by-step installation wizard with:
  - Welcome screen
  - System requirements check
  - Folder permissions verification
  - Database configuration with connection testing
  - Administrator account creation
  - Automatic installation process
  - Installation lock mechanism for security

### Changed
- Updated `.htaccess` with enhanced security headers
- Improved `index.php` to handle both installation and normal operation
- Version bump to 1.3.0

### Security
- Added installation lock file mechanism
- Enhanced `.htaccess` security rules
- Protected sensitive files from direct access
- Added SQL injection and XSS protection headers

---

## [1.2.0] - 2025-07-03

### Added
- Template management with header/footer images
- Document management with rich text editor
- TinyMCE integration
- Auto reference number generation (ZOI/LTR/YYYY/000001)
- PDF generation engine
- Dashboard with statistics
- Settings management

### Features
- **Phase 2 - PDF Generation**
- **Phase 3 - Production Ready**

---

## [1.1.0] - 2025-07-02

### Added
- Basic template CRUD operations
- Basic document CRUD operations
- Bootstrap 5 responsive UI
- CSRF and XSS protection

---

## [1.0.0] - 2025-07-01

### Added
- Initial release
- Laravel framework setup
- Basic MVC structure
- Authentication scaffolding
