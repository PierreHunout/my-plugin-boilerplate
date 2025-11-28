# My Plugin Boilerplate

A modern WordPress plugin boilerplate with PSR-4 autoloading, composer dependencies, and development tools.

## Description

This plugin serves as a starting point for developing WordPress plugins with modern PHP practices. It includes:

- PSR-4 autoloading
- Composer dependency management
- PHP_CodeSniffer for code quality
- PHPStan for static analysis
- Internationalization (i18n) support
- Proper WordPress coding standards compliance

## Features

- PSR-4 autoloading for clean code organization
- Composer integration for dependency management
- **WordPress Coding Standards** (WordPress, WordPress-Core, WordPress-Docs, WordPress-Extra)
- **Automated Code Quality** with PHP_CodeSniffer and WordPress standards
- **Complete PHPUnit Testing Suite** with unit and integration tests
- **Code Coverage Reports** for quality assurance
- **Composer Scripts** for streamlined development workflow
- **Debug and Logging System** with secure JSON logging to `wp-content/`
- **Visual Debug Output** with WordPress-style formatting
- WordPress Blocks development with @wordpress/scripts
- Both static and dynamic blocks examples included
- **Automatic Block Registration** system with WP_Filesystem
- Modern JavaScript ES6+ and React support
- SCSS/CSS compilation and optimization
- WordPress i18n ready with proper textdomain
- Security checks preventing direct file access
- Modern PHP 7.4+ compatibility
- WordPress 5.0+ compatibility

## Installation

### From Source

1. Clone or download this repository to your WordPress plugins directory:

   ```bash
   cd wp-content/plugins/
   git clone https://github.com/PierreHunout/my-plugin-boilerplate.git
   ```

2. Install dependencies using Composer and npm:

   ```bash
   cd my-plugin-boilerplate
   composer install  # PHP dependencies
   npm install       # JavaScript dependencies
   ```

3. Build the WordPress blocks:

   ```bash
   npm run build
   ```

4. Activate the plugin through the WordPress admin interface.

### Manual Installation

1. Download the plugin files
2. Upload the `my-plugin-boilerplate` folder to `/wp-content/plugins/`
3. Run `composer install` and `npm install` in the plugin directory
4. Build blocks with `npm run build`
5. Activate the plugin through the 'Plugins' menu in WordPress

## Requirements

- PHP 7.4 or higher
- WordPress 5.0 or higher
- Node.js 14+ and npm (for block development)
- Composer (for PHP dependency management)

## Development

### Code Standards & Quality Checks

This plugin follows WordPress coding standards with comprehensive PHPCS integration:

**Using Composer Scripts (Recommended):**

```bash
# Check code standards (WordPress, WordPress-Core, WordPress-Docs, WordPress-Extra)
composer phpcs

# Auto-fix code standards issues
composer phpcs:fix

# Generate detailed summary report
composer phpcs:report

# Run all quality checks (PHPCS + PHPUnit)
composer test

# Run quick tests (PHPCS + Unit tests only)
composer test:quick
```

### Testing with PHPUnit

The plugin includes a complete PHPUnit testing setup with WordPress integration:

**PHPUnit Commands:**

```bash
# Run all tests (unit + integration)
composer phpunit

# Run only unit tests (faster, no WordPress required)
composer phpunit:unit

# Run only integration tests (requires WordPress test environment)
composer phpunit:integration

# Generate code coverage report
composer phpunit:coverage
```

**Direct PHPUnit commands:**

```bash
# Run all tests
./vendor/bin/phpunit

# Run specific test suite
./vendor/bin/phpunit --testsuite="Unit Tests"

# Run with coverage
./vendor/bin/phpunit --coverage-html tests/coverage-html
```

#### Testing Structure

```text
tests/
├── bootstrap.php           # PHPUnit bootstrap file
├── unit/                   # Unit tests (no WordPress required)
│   └── HelpersTest.php     # Example unit test
├── integration/            # Integration tests (WordPress required)
│   └── PluginIntegrationTest.php
├── logs/                   # Test logs and reports
└── coverage-html/          # Code coverage reports (generated)
```

#### WordPress Test Environment

For integration tests, you can set up the WordPress test environment:

```bash
# Download WordPress test suite (optional)
bash bin/install-wp-tests.sh wordpress_test root '' localhost latest

# Set environment variable
export WP_TESTS_DIR=/tmp/wordpress-tests-lib
```

**Direct commands:**

```bash
# Check code standards
./vendor/bin/phpcs

# Auto-fix code standards issues
./vendor/bin/phpcbf
```

#### PHPCS Configuration

The plugin includes a comprehensive PHPCS configuration (`phpcs.xml.dist`) with:

- WordPress coding standards (WordPress, WordPress-Core, WordPress-Docs, WordPress-Extra)
- Variable analysis for unused variables detection
- PHP 7.4+ compatibility checks
- WordPress 5.0+ minimum version support
- Automatic exclusion of vendor, node_modules, build directories
- Parallel processing for faster analysis
- Custom prefixes configuration for WordPress globals

### Project Structure

```text
my-plugin-boilerplate/
├── assets/              # CSS, JS, and image assets
├── build/               # Compiled WordPress blocks
│   ├── dynamic/         # Built dynamic blocks
│   ├── static/          # Built static blocks
│   └── tailwind-styles.css # Compiled Tailwind CSS
├── includes/            # PHP classes (PSR-4 autoloaded)
│   ├── Action/          # WordPress actions and hooks
│   ├── Admin/           # Admin interface classes
│   │   └── Sections/    # Settings page sections (Features, Debug, etc.)
│   ├── Blocks/          # Block registration and management
│   ├── Debug/           # Logging and debug utilities
│   ├── Login/           # User authentication features
│   ├── PostType/        # Custom post types
│   └── Utils/           # Utility classes and helpers
├── languages/           # Translation files
├── node_modules/        # Node.js dependencies
├── src/                 # WordPress blocks source code
│   ├── dynamic/         # Dynamic (server-rendered) blocks
│   ├── static/          # Static (client-rendered) blocks
│   └── tailwind.css     # Tailwind CSS entry point
├── vendor/              # Composer dependencies
├── tests/               # PHPUnit test suite
│   ├── bootstrap.php    # Test bootstrap file
│   ├── unit/            # Unit tests
│   ├── integration/     # Integration tests
│   └── logs/            # Test logs and reports
├── composer.json        # Composer configuration
├── package.json         # Node.js dependencies and scripts
├── tailwind.config.js   # Tailwind CSS configuration with custom palette
├── postcss.config.js    # PostCSS configuration for Tailwind
├── webpack.config.js    # Webpack configuration for blocks
├── .prettierrc          # Prettier code formatting configuration
├── phpcs.xml.dist       # PHPCS configuration (WordPress standards)
├── phpcs.xml            # Local PHPCS configuration (gitignored)
├── phpunit.xml.dist     # PHPUnit configuration
├── phpunit.xml          # Local PHPUnit configuration (gitignored)
├── package-lock.json    # npm lock file
├── .gitignore           # Git ignore rules
├── my-plugin-boilerplate.php  # Main plugin file
├── uninstall.php        # Plugin cleanup on uninstall
├── readme.txt           # WordPress.org repository readme
└── README.md            # This file

# Generated at runtime (wp-content level):
wp-content/
└── my-plugin-boilerplate-logs/  # Secure debug logs (outside plugin)
    ├── .htaccess                # Access protection
    ├── index.php                # Directory browsing protection
    └── *.json                   # Timestamped log files
```

### Autoloading

The plugin uses PSR-4 autoloading via Composer. All classes should be placed in the `includes/` directory and use the `MyPluginBoilerplate` namespace.

Example:

```php
<?php
namespace MyPluginBoilerplate;

class ExampleClass {
    // Your code here
}
```

### Internationalization

The plugin is translation-ready with the textdomain `my-plugin-boilerplate`. All translatable strings use proper WordPress i18n functions with:

- Ordered placeholders for multi-placeholder strings
- Translators comments for placeholder clarification
- Proper textdomain usage

## Debug and Logging System

The plugin includes a comprehensive logging system for development and debugging purposes.

### Log System Features

- **Secure logging** to `wp-content/my-plugin-boilerplate-logs/`
- **Debug mode control** via `MY_PLUGIN_BOILERPLATE_DEBUG` constant
- **Structured JSON logging** with timestamps, data types, and metadata
- **Automatic security protection** with `.htaccess` and `index.php`
- **Fallback error logging** when file writing fails
- **WordPress filesystem integration** for secure file operations

### Enabling Debug Mode

Add this constant to your `wp-config.php` or define it in the plugin:

```php
define( 'MY_PLUGIN_BOILERPLATE_DEBUG', true );
```

### Using the Log System

#### File Logging

Log data to JSON files with structured information:

```php
use MyPluginBoilerplate\Debug\Log;

// Log simple data
Log::log( 'user_action', 'User clicked button' );

// Log complex data structures
Log::log( 'form_data', [
    'user_id' => get_current_user_id(),
    'form_fields' => $_POST,
    'timestamp' => current_time( 'mysql' )
] );

// Log error information
Log::log( 'error', [
    'message' => $exception->getMessage(),
    'file' => $exception->getFile(),
    'line' => $exception->getLine()
] );
```

#### Visual Debug Output

Display formatted debug information on screen (only when debug is enabled):

```php
use MyPluginBoilerplate\Debug\Log;

// Display data and continue execution
Log::print( $data );

// Display data and stop execution
Log::print( $data, true );
```

### Log File Structure

Log files are automatically organized with security in mind:

```text
wp-content/
└── my-plugin-boilerplate-logs/
    ├── .htaccess              # Blocks direct access to log files
    ├── index.php              # Prevents directory browsing
    ├── user_action-1729123456.json
    ├── form_data-1729123789.json
    └── error-1729124012.json
```

### JSON Log Format

Each log entry contains structured information:

```json
{
    "date": "2024-10-16 14:30:45+00:00",
    "type": "array",
    "data": {
        "user_id": 1,
        "action": "form_submission",
        "details": "Contact form submitted successfully"
    }
}
```

### Security Features

- **Protected directory**: `.htaccess` prevents direct access to log files
- **No directory browsing**: `index.php` blocks directory listing
- **Debug-only operation**: Logs only when `MY_PLUGIN_BOILERPLATE_DEBUG` is enabled
- **Secure file permissions**: `0755` for directories, `0644` for files
- **WordPress filesystem**: Uses `WP_Filesystem` for secure file operations

### Log Management

**Automatic cleanup:** Log files persist across plugin updates and are stored outside the plugin directory.

**Manual cleanup:** To remove all logs:

```bash
# Remove all log files
rm -rf wp-content/my-plugin-boilerplate-logs/
```

**Production safety:** Logs are automatically disabled in production when `MY_PLUGIN_BOILERPLATE_DEBUG` is `false`.

### Debug Output Features

The visual debug output includes:

- **Data type detection** (string, array, object, etc.)
- **JSON formatting** for readability
- **WordPress-style UI** with proper escaping
- **Conditional display** (only when debug mode is enabled)
- **Safe termination** using `wp_die()` instead of `die()`

## Code Quality Checks

**PHP Quality Checks:**

```bash
# Install PHP dependencies
composer install

# Run all quality checks (recommended)
composer test                # PHPCS + PHPUnit (all tests)
composer test:quick          # PHPCS + Unit tests only

# Individual checks:
composer phpcs               # Check WordPress coding standards
composer phpcs:fix           # Auto-fix coding standards
composer phpcs:report        # Detailed summary report

# Testing:
composer phpunit             # Run all tests
composer phpunit:unit        # Unit tests only
composer phpunit:integration # Integration tests only
composer phpunit:coverage    # Generate coverage report
```

**Alternative direct commands:**

```bash
./vendor/bin/phpcs           # Check coding standards
./vendor/bin/phpcbf          # Auto-fix coding standards
./vendor/bin/phpunit         # Run PHPUnit tests
```

**JavaScript/WordPress Blocks Quality Checks:**

```bash
# Install JavaScript dependencies
npm install

# Lint JavaScript code
npm run lint:js

# Lint CSS/SCSS code
npm run lint:css

# Format code automatically
npm run format

# Build blocks for production
npm run build

# Test blocks in development mode
npm start
```

## License

This project is licensed under the GPL-3.0 License - see the [LICENSE](https://www.gnu.org/licenses/gpl-3.0.html) for details.

---

## Author

### Pierre Hunout

- Website: [https://pierrehunout.com/](https://pierrehunout.com/)
- GitHub: [@PierreHunout](https://github.com/PierreHunout)

---

## Support

For support, feature requests, or bug reports, please use the [GitHub Issues](https://github.com/PierreHunout/my-plugin-boilerplate/issues) page.
