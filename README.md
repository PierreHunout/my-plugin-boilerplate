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
- ✅ PHP_CodeSniffer integration for code standards
- ✅ PHPStan for static code analysis
- ✅ WordPress i18n ready with proper textdomain
- ✅ Security checks preventing direct file access
- ✅ Modern PHP 7.4+ compatibility
- ✅ WordPress 5.0+ compatibility

## Installation

### From Source

1. Clone or download this repository to your WordPress plugins directory:
   \`\`\`bash
   cd wp-content/plugins/
   git clone <https://github.com/PierreHunout/my-plugin-boilerplate.git>
   \`\`\`

2. Install dependencies using Composer:
   \`\`\`bash
   cd my-plugin-boilerplate
   composer install
   \`\`\`

3. Activate the plugin through the WordPress admin interface.

### Manual Installation

1. Download the plugin files
2. Upload the \`my-plugin-boilerplate\` folder to \`/wp-content/plugins/\`
3. Run \`composer install\` in the plugin directory
4. Activate the plugin through the 'Plugins' menu in WordPress

## Requirements

- PHP 7.4 or higher
- WordPress 5.0 or higher  
- Composer (for dependency management)

## Development

### Code Standards

This plugin follows WordPress coding standards and uses PHP_CodeSniffer for validation:

\`\`\`bash

### Check code standards

./vendor/bin/phpcs

### Auto-fix code standards issues

./vendor/bin/phpcbf
\`\`\`

### Static Analysis

PHPStan is configured for static code analysis:

\`\`\`bash

### Run PHPStan analysis

./vendor/bin/phpstan analyse
\`\`\`

### Project Structure

\`\`\`
my-plugin-boilerplate/
├── assets/              # CSS, JS, and image assets
├── includes/            # PHP classes (PSR-4 autoloaded)
├── languages/           # Translation files
├── vendor/              # Composer dependencies
├── composer.json        # Composer configuration
├── phpstan.neon        # PHPStan configuration
├── my-plugin-boilerplate.php  # Main plugin file
└── README.md           # This file
\`\`\`

### Autoloading

The plugin uses PSR-4 autoloading via Composer. All classes should be placed in the \`includes/\` directory and use the \`MyPluginBoilerplate\` namespace.

Example:
\`\`\`php
<?php
namespace MyPluginBoilerplate;

class ExampleClass {
    // Your code here
}
\`\`\`

### Internationalization

The plugin is translation-ready with the textdomain \`my-plugin-boilerplate\`. All translatable strings use proper WordPress i18n functions with:

- Ordered placeholders for multi-placeholder strings
- Translators comments for placeholder clarification
- Proper textdomain usage

## Contributing

1. Fork the repository
2. Create a feature branch (\`git checkout -b feature/amazing-feature\`)
3. Make your changes following the coding standards
4. Run tests and code analysis
5. Commit your changes (\`git commit -m 'Add amazing feature'\`)
6. Push to the branch (\`git push origin feature/amazing-feature\`)
7. Open a Pull Request

### Code Quality Checks

Before submitting a PR, ensure your code passes all quality checks:

\`\`\`bash
# Install dependencies
composer install

# Check coding standards
./vendor/bin/phpcs

# Run static analysis
./vendor/bin/phpstan analyse

# Auto-fix coding standards (if possible)
./vendor/bin/phpcbf
\`\`\`

## License

This project is licensed under the GPL-3.0 License - see the [LICENSE](https://www.gnu.org/licenses/gpl-3.0.html) for details.

## Author

=== Pierre Hunout ===

- Website: [https://pierrehunout.com/](https://pierrehunout.com/)
- GitHub: [@PierreHunout](https://github.com/PierreHunout)

## Changelog

### 1.0.0
- Initial release
- PSR-4 autoloading implementation
- Composer integration
- Code quality tools integration
- WordPress i18n compliance
- Security enhancements

## Support

For support, feature requests, or bug reports, please use the [GitHub Issues](https://github.com/PierreHunout/my-plugin-boilerplate/issues) page.

## Acknowledgments

- WordPress community for coding standards and best practices
- Composer for dependency management
- PHP_CodeSniffer and PHPStan for code quality tools" > "/Users/pierrehunout/Local Sites/testplugins/app/public/wp-content/plugins/my-plugin-boilerplate/README.md
