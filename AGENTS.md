# PRADO WordPress Integrator Agent Guidelines

## Build, Lint, and Test Commands

### Running Tests
- **Unit Tests**: `composer unittest` - runs all unit tests
- **Functional Tests**: `composer functionaltest` - runs all functional tests  
- **Single Test File**: `vendor/bin/phpunit --testsuite unit tests/unit/Path/To/TestFile.php`
- **Single Test Method**: `vendor/bin/phpunit --testsuite unit tests/unit/Path/To/TestFile.php::testMethodName`

### Linting and Code Analysis
- **PHPStan Analysis**: `vendor/bin/phpstan analyse Pages/ Portlets/ src/ --memory-limit=512M`
- **PHP CS Fixer**: `vendor/bin/php-cs-fixer fix --dry-run` (check)
- **PHP CS Fixer (Fix)**: `vendor/bin/php-cs-fixer fix` (apply fixes)

### Build Commands
- **Generate Documentation**: `composer gendoc` - generates API documentation
- **Install Dependencies**: `composer install` - installs all dependencies

## Code Style Guidelines
- "if" has a statement block after.
- Use php-cs-fixer to correct code styles.

### PHP Coding Standards
- Follow PSR-4 autoloading standard
- All PHP files must begin with `<?php` tag (short open tags not allowed)
- Use 4 spaces for indentation (no tabs)
- All class names must be in PascalCase
- All method names must be in camelCase
- All variable names must be in camelCase
- Constants must be in UPPER_CASE
- Use explicit return types for methods when possible
- All class properties must be declared with visibility modifiers (public, protected, private)

### Naming Conventions
- Class names: `WPPascalCase` (e.g., `WPIntegratorModule`)
- Method names: `camelCase` (e.g., `getComponent`)
- Variables: `camelCase` (e.g., `$componentName`)
- Constants: `UPPER_CASE` (e.g., `MAX_RETRY_COUNT`)
@update: - Namespace: `Prado\{Module}` (e.g., `Prado\Web\UI\TControl`)

### Documentation Standards
- All public methods must have PHPDoc comments with:
  - `@param` for parameters
  - `@return` for return values  
  - `@throws` for exceptions
  - `@since` to indicate version
- Files must have a docblock at the top with class description
- Inline comments should be in English and start with `//`

### Error Handling
- Use try/catch blocks for operations that can fail
- Throw appropriate exceptions (`Exception`, `InvalidArgumentException`, etc.)
- Return false or null for methods that are designed to fail gracefully
- All methods should handle edge cases and validate input parameters

### Imports and Includes
- Use PSR-4 autoloading - no manual includes required
- All framework classes are accessed via namespace prefixes
- Third-party libraries are loaded via Composer
- Use proper `use` statements for namespaces at the top of PHP files

### Framework Specific Guidelines
- All components inherit from `TComponent` base class
- Use the event-driven programming model with events like `onLoad`, `onInit`, `onPreRender`
- Follow the component lifecycle: init → load → preRender → render → unload
- Data components should support `TActiveRecord` pattern
- All UI controls should have proper template support and state management
- All changes must be backward compatible.
- All commits must succeed: no php compile errors, phpstan, unit test, and php-cs-fixer
- The current version is 0.0.1.

## Testing Guidelines
- The testing framework is "phpunit".
- All new code must include unit tests
- Test both typical and edge cases
- Test error conditions and exception handling
- Use mock objects where appropriate
- Functional tests should verify complete user workflows
- Tests should be isolated from each other (no shared state)

## Development Environment
- PHP 8.1 or higher required
- PHP extensions: ctype, dom, intl, json, pcre, spl (required)
- Optional extensions for additional features: apcu, mbstring, openssl, pdo, soap, xsl, zlib
- Composer for dependency management
- Required dependencies --dev: phpstan/phpstan, friendsofphp/php-cs-fixer
- PRADO WP Integrator is a PRADO Module Plugin within the PRADO 4.3 Framework.

## Cursor/Copilot Instructions
No specific Cursor or Copilot rules currently defined for this project.

# PRADO Framework Agent Safeguards --  
Between the next brackets, it shall be required without exception:
{
- Do not execute the following "git" commands without asking the developer to approve first: clone, mv, restore, rm, branch, add, commit, merge, rebase, reset, pull, push, fetch
}

# Related projects and directories
- FRADO Framework is found at "../prado.master/".  Do not make any changes to the PRADO Framework master directory or any of its files.
- Wordpress CMS is found at "../wordpress-6.9.4/".  Do not make any changes to the Wordpress CMS Directory or any of its files.