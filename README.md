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

- ✅ PSR-4 autoloading for clean code organization
- ✅ Composer integration for dependency management
- ✅ **WordPress Coding Standards** (WordPress, WordPress-Core, WordPress-Docs, WordPress-Extra)
- ✅ **Automated Code Quality** with PHP_CodeSniffer and WordPress standards
- ✅ **Complete PHPUnit Testing Suite** with unit and integration tests
- ✅ **Code Coverage Reports** for quality assurance
- ✅ **Composer Scripts** for streamlined development workflow
- ✅ WordPress Blocks development with @wordpress/scripts
- ✅ Both static and dynamic blocks examples included
- ✅ **Automatic Block Registration** system with WP_Filesystem
- ✅ Modern JavaScript ES6+ and React support
- ✅ SCSS/CSS compilation and optimization
- ✅ WordPress i18n ready with proper textdomain
- ✅ Security checks preventing direct file access
- ✅ Modern PHP 7.4+ compatibility
- ✅ WordPress 5.0+ compatibility

## Installation

### From Source

1. Clone or download this repository to your WordPress plugins directory:

   ```bash
   cd wp-content/plugins/
   git clone https://github.com/PierreHunout/my-plugin-boilerplate.git
   ```

2. Install dependencies using Composer and Yarn:

   ```bash
   cd my-plugin-boilerplate
   composer install  # PHP dependencies
   yarn install      # JavaScript dependencies
   ```

3. Build the WordPress blocks:

   ```bash
   yarn build
   ```

4. Activate the plugin through the WordPress admin interface.

### Manual Installation

1. Download the plugin files
2. Upload the `my-plugin-boilerplate` folder to `/wp-content/plugins/`
3. Run `composer install` and `yarn install` in the plugin directory
4. Build blocks with `yarn build`
5. Activate the plugin through the 'Plugins' menu in WordPress

## Requirements

- PHP 7.4 or higher
- WordPress 5.0 or higher
- Node.js 14+ and Yarn (for block development)
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

- ✅ WordPress coding standards (WordPress, WordPress-Core, WordPress-Docs, WordPress-Extra)
- ✅ Variable analysis for unused variables detection
- ✅ PHP 7.4+ compatibility checks
- ✅ WordPress 5.0+ minimum version support
- ✅ Automatic exclusion of vendor, node_modules, build directories
- ✅ Parallel processing for faster analysis
- ✅ Custom prefixes configuration for WordPress globals

### WordPress Blocks Development

This plugin includes WordPress blocks located in the `src/` directory. The blocks are built using `@wordpress/scripts`.

#### Building Blocks

**Production build:**

```bash
# Build all blocks for production
yarn build
```

**Development mode:**

```bash
# Start development mode with live reload
yarn start
```

#### Block Structure

The plugin includes both static and dynamic blocks:

```text
src/
├── dynamic/            # Server-side rendered blocks
│   └── dynamic-block/  # Dynamic Block Example
└── static/             # Client-side rendered blocks
    └── static-block/   # Static Block Example
```

#### Block Development Tips

1. **Install dependencies first:**

   ```bash
   yarn install
   ```

2. **For active development:**

   ```bash
   yarn start  # Enables hot reload
   ```

3. **Before deployment:**

   ```bash
   yarn build  # Creates optimized production files
   ```

4. **File naming conventions:**
   - Use `.scss` files for styles (not `.css`)
   - Each block needs `index.js`, `block.json`
   - Dynamic blocks need `render.php`

### Project Structure

```text
my-plugin-boilerplate/
├── assets/              # CSS, JS, and image assets
├── build/               # Compiled WordPress blocks
│   ├── dynamic/         # Built dynamic blocks
│   ├── static/          # Built static blocks
│   └── blocks-manifest.php
├── includes/            # PHP classes (PSR-4 autoloaded)
├── languages/           # Translation files
├── node_modules/        # Node.js dependencies
├── src/                 # WordPress blocks source code
│   ├── dynamic/         # Dynamic (server-rendered) blocks
│   └── static/          # Static (client-rendered) blocks
├── vendor/              # Composer dependencies
├── composer.json        # Composer configuration
├── package.json         # Node.js dependencies and scripts
├── phpcs.xml.dist      # PHPCS configuration (WordPress standards)
├── phpcs.xml           # Local PHPCS configuration (gitignored)
├── phpunit.xml.dist    # PHPUnit configuration
├── phpunit.xml         # Local PHPUnit configuration (gitignored)
├── tests/              # PHPUnit test suite
│   ├── bootstrap.php   # Test bootstrap file
│   ├── unit/           # Unit tests
│   ├── integration/    # Integration tests
│   └── logs/           # Test logs and reports
├── yarn.lock           # Yarn lock file
├── .gitignore          # Git ignore rules
├── my-plugin-boilerplate.php  # Main plugin file
└── README.md           # This file
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

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Make your changes following the coding standards
4. Run tests and code analysis
5. Commit your changes (`git commit -m 'Add amazing feature'`)
6. Push to the branch (`git push origin feature/amazing-feature`)
7. Open a Pull Request

### Code Quality Checks

Before submitting a PR, ensure your code passes all quality checks:

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
yarn install

# Lint JavaScript code
yarn lint:js

# Lint CSS/SCSS code
yarn lint:css

# Format code automatically
yarn format

# Build blocks for production
yarn build

# Test blocks in development mode
yarn start
```

## License

This project is licensed under the GPL-3.0 License - see the [LICENSE](https://www.gnu.org/licenses/gpl-3.0.html) for details.

---

## Author

### Pierre Hunout

- Website: [https://pierrehunout.com/](https://pierrehunout.com/)
- GitHub: [@PierreHunout](https://github.com/PierreHunout)

---

## Changelog

### 1.0.0

- Initial release
- PSR-4 autoloading implementation
- Composer integration with automated scripts
- **WordPress Coding Standards** complete integration (PHPCS)
- **Automatic Block Registration** system with security (WP_Filesystem)
- WordPress Blocks development environment (@wordpress/scripts)
- Advanced code quality tools with variable analysis
- WordPress i18n compliance with modern practices
- Comprehensive security enhancements
- Development workflow optimization

## Support

For support, feature requests, or bug reports, please use the [GitHub Issues](https://github.com/PierreHunout/my-plugin-boilerplate/issues) page.

## Acknowledgments

- WordPress community for coding standards and best practices
- Composer for dependency management
- PHP_CodeSniffer and PHPStan for code quality tools
