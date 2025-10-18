=== My Plugin Boilerplate ===
Contributors: pierrehunout
Donate link: https://pierrehunout.com/donate
Tags: boilerplate, plugin development, wordpress, psr-4, composer, blocks
Requires at least: 5.0
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPL-3.0
License URI: https://www.gnu.org/licenses/gpl-3.0.html

A modern WordPress plugin boilerplate with PSR-4 autoloading, Composer dependencies, WordPress blocks, and comprehensive development tools.

== Description ==

My Plugin Boilerplate is a comprehensive starting point for modern WordPress plugin development. It includes all the tools and best practices you need to build professional, maintainable WordPress plugins.

**Key Features:**

* **Modern PHP Architecture** - PSR-4 autoloading with Composer dependency management
* **WordPress Blocks Support** - Complete development environment for Gutenberg blocks
* **Code Quality Tools** - PHPCS with WordPress coding standards and PHPUnit testing
* **Security First** - Built-in security measures and WordPress best practices
* **Developer Friendly** - Comprehensive documentation and development workflow
* **i18n Ready** - Full internationalization support with proper textdomains

**What's Included:**

* PSR-4 autoloading for clean code organization
* Composer integration for dependency management
* WordPress Coding Standards (PHPCS) configuration
* PHPUnit testing suite with unit and integration tests
* WordPress Blocks development with @wordpress/scripts
* Both static and dynamic blocks examples
* Automatic block registration system
* Modern JavaScript ES6+ and React support
* SCSS/CSS compilation and optimization
* Security checks preventing direct file access
* Comprehensive error handling and logging
* WordPress i18n compliance

**Perfect For:**

* Plugin developers looking for a modern foundation
* Teams wanting consistent coding standards
* Projects requiring comprehensive testing
* Developers building custom Gutenberg blocks
* Anyone following WordPress best practices

== Installation ==

**From WordPress Admin:**

1. Go to Plugins > Add New
2. Search for "My Plugin Boilerplate"
3. Install and activate the plugin

**Manual Installation:**

1. Download the plugin files
2. Upload the `my-plugin-boilerplate` folder to `/wp-content/plugins/`
3. Activate the plugin through the 'Plugins' menu in WordPress

**For Development:**

1. Clone the repository to your plugins directory
2. Run `composer install` to install PHP dependencies
3. Run `yarn install` to install JavaScript dependencies
4. Run `yarn build` to compile blocks
5. Activate the plugin in WordPress admin

== Frequently Asked Questions ==

= What PHP version is required? =

This plugin requires PHP 7.4 or higher. We recommend using the latest stable version of PHP for optimal performance and security.

= Does this work with the latest WordPress version? =

Yes, this plugin is tested with the latest WordPress versions and follows WordPress coding standards and best practices.

= Can I use this for commercial projects? =

Absolutely! This plugin is released under the GPL-3.0 license, which allows commercial use. Please review the license terms for full details.

= How do I customize the blocks? =

The plugin includes example blocks in the `src/` directory. You can modify these or create new ones following the same structure. Run `yarn start` for development mode with live reload.

= Is this plugin production-ready? =

This is a boilerplate/starter template designed for developers. While it includes production-ready code patterns, you should customize it for your specific needs before using in production.

= How do I run the tests? =

Use the following commands:
* `composer test` - Run all quality checks (PHPCS + PHPUnit)
* `composer phpunit:unit` - Run unit tests only
* `composer phpcs` - Check coding standards

= Does this support multisite? =

Yes, the plugin is designed to work with both single-site and multisite WordPress installations.

== Screenshots ==

1. Plugin activation screen
2. Example block in the Gutenberg editor
3. Plugin settings page
4. Development tools overview

== Changelog ==

= 1.0.0 =
* Initial release
* PSR-4 autoloading implementation
* Composer integration with automated scripts
* WordPress Coding Standards complete integration (PHPCS)
* PHPUnit testing suite with unit and integration tests
* Automatic Block Registration system with security (WP_Filesystem)
* WordPress Blocks development environment (@wordpress/scripts)
* Code quality tools integration
* WordPress i18n compliance with modern practices
* Comprehensive security enhancements
* Development workflow optimization
* Complete documentation and examples

== Upgrade Notice ==

= 1.0.0 =
Initial release. No upgrade necessary.

== Developer Notes ==

**Development Setup:**

1. Clone the repository
2. Run `composer install`
3. Run `yarn install`
4. Run `yarn build` or `yarn start` for development

**Testing:**

* PHP: `composer test` or `composer phpunit`
* JavaScript: `yarn test` (if configured)
* Code Standards: `composer phpcs`

**Building for Production:**

* Run `yarn build` to compile optimized assets
* Ensure all tests pass with `composer test`
* Check coding standards with `composer phpcs`

**Contributing:**

Contributions are welcome! Please follow WordPress coding standards and include tests for new functionality.

**Support:**

For development questions and support, please visit the GitHub repository or contact the developer.

== Third Party Libraries ==

This plugin uses the following third-party libraries:

* **Composer** - Dependency management for PHP
* **PHPUnit** - PHP testing framework
* **PHP_CodeSniffer** - PHP code analysis
* **WordPress Coding Standards** - PHPCS rules for WordPress
* **@wordpress/scripts** - Build tools for WordPress blocks
* **Yoast PHPUnit Polyfills** - PHPUnit compatibility

All libraries are included in their respective licenses and are compatible with GPL-3.0.

== Privacy Policy ==

This plugin does not collect, store, or transmit any personal data. It operates entirely within your WordPress installation and follows WordPress privacy best practices.

== Technical Requirements ==

* **WordPress:** 5.0 or higher
* **PHP:** 7.4 or higher
* **MySQL:** 5.6 or higher (or MariaDB 10.0+)
* **For Development:**
  * Node.js 14+ and Yarn
  * Composer
  * Git (recommended)

== Links ==

* [GitHub Repository](https://github.com/PierreHunout/my-plugin-boilerplate)
* [Developer Website](https://pierrehunout.com/)
* [Documentation](https://github.com/PierreHunout/my-plugin-boilerplate/wiki)
* [Issue Tracker](https://github.com/PierreHunout/my-plugin-boilerplate/issues)