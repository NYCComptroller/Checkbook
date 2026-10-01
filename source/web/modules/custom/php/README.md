# PHP Filter

Allows embedded PHP code/snippets to be evaluated via a Text Format filter, plus additional “experts only” PHP-evaluation integrations (block visibility conditions and Views contextual filter plugins).

**Warning:** This module executes arbitrary PHP code. Enabling it can cause severe security and performance issues and should only be used in tightly controlled environments.

## Table of Contents

- [Module Purpose](#module-purpose)
- [Module Functionality](#module-functionality)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Testing](#testing)

## Module Purpose

The `php` module (PHP Filter) provides a Drupal Text Format filter (`php_code`) that evaluates embedded PHP code when rendering content.

It is intended for cases where trusted administrators need to inject small, dynamic behaviors into content without writing a full custom module/theme. Because it relies on `eval()`, it must be treated as a last resort and restricted to highly trusted users only.

## Module Functionality

Key components introduced by this module:

- **PHP evaluator Text Format filter**
  - Filter plugin: `Drupal\php\Plugin\Filter\Php` (`@Filter(id = "php_code")`)
  - Processing calls `php_eval($text)` and disables caching (`FilterProcessResult::setCacheMaxAge(0)`).
  - A default Text Format is shipped via config install: `config/install/filter.format.php_code.yml` (format ID: `php_code`).

- **Safe(ish) wrapper around `eval()`**
  - Procedural helper: `php_eval($code)` (in `php.module`).
  - Uses output buffering and evaluates the provided text as if it were a standalone PHP file by prepending `?>`.
  - Requires the snippet to include `<?php ?>` tags.

- **Block visibility “PHP” condition**
  - Condition plugin: `Drupal\php\Plugin\Condition\Php` (`@Condition(id = "php")`).
  - Lets site builders restrict blocks based on a PHP snippet that must `return TRUE;` or `FALSE;`.
  - Editing the snippet is gated behind the permission `use PHP for settings`.

- **Views contextual filter plugins (advanced usage)**
  - Default argument plugin: `Drupal\php\Plugin\views\argument_default\Php`
  - Argument validator plugin: `Drupal\php\Plugin\views\argument_validator\Php`
  - Both run admin-provided PHP snippets via `eval()`.
  - Access to configure is gated behind the permission `use PHP for settings`.

- **Admin UX enhancement for Blocks UI**
  - Library: `php/php.block.admin` (`php.libraries.yml`) loads `js/php.admin.js`.
  - `hook_library_info_alter()` in `php.module` automatically adds this library when `block/drupal.block` is attached.
  - This provides a vertical-tab summary for the PHP visibility condition.

- **Update system behavior**
  - `hook_update_projects_alter()` unsets the `php` project, disabling update status checks for this module.

## Installation

1. Ensure the core dependency is enabled:
   - `drupal:filter`

2. Enable the module:
   - Admin UI: `Extend` (`/admin/modules`)
   - Or Drush: `drush en php`

3. After install, verify the default Text Format was created:
   - Navigate to `Configuration` → `Content authoring` → `Text formats and editors` (`/admin/config/content/formats`)
   - Confirm a format named **PHP code** exists (machine name `php_code`).

## Configuration

Most configuration is done via core UI surfaces:

- **Text formats and editors**
  - Location: `/admin/config/content/formats`
  - The module ships `PHP code` (`php_code`) with the `php_code` filter enabled.
  - Only grant access to the `PHP code` format to trusted roles.

- **Permissions**
  - `use PHP for settings` (from `php.permissions.yml`)
    - Controls whether a user can enter/modify PHP snippets in “settings” UIs such as:
      - Block visibility condition configuration (`visibility[php][php]`)
      - Views argument default/validator configuration forms
    - Marked `restrict access: true` and should remain limited to highly trusted administrators.

## Usage

### Using the PHP evaluator in content

1. Create/edit content (e.g., a Basic page).
2. Set the body text format to **PHP code**.
3. Enter PHP wrapped in `<?php ?>` tags.

Example snippet:

```php
<?php
print "Hello from PHP Filter";
?>
```

### Using the PHP block visibility condition

1. Go to `Structure` → `Block layout` and place/edit a block.
2. In the block’s **Visibility** settings, choose **PHP**.
3. Enter a snippet that returns `TRUE` when the block should be shown.

Example:

```php
<?php
return \Drupal::currentUser()->isAuthenticated();
?>
```

### Using PHP in Views contextual filters (advanced)

In a View with a contextual filter:

- Use **PHP Code** as a default argument plugin to compute a value.
- Use **PHP Code** as an argument validator to accept/reject an argument.

These features are intended for expert-only use and require `use PHP for settings`.

## Testing

### Manual Testing

1. **Scenario 1: PHP code executes in a Text Format**
  - Description: Verify that PHP is evaluated only when the **PHP code** format is selected.
  - Steps:
    1. Create a page whose body contains PHP wrapped in `<?php ?>` tags.
    2. View the page with a non-PHP format selected and confirm the PHP is displayed as text.
    3. Switch the body format to **PHP code** and view again.
  - Expected Result: When using **PHP code**, the PHP output is rendered and the raw PHP is not displayed.

2. **Scenario 2: Block visibility condition using PHP**
  - Description: Verify a block can be conditionally shown based on a snippet returning `TRUE`/`FALSE`.
  - Steps:
    1. Place a block and add Visibility → PHP with a snippet returning `FALSE`.
    2. Visit a page where the block would appear.
    3. Change the snippet to return `TRUE`.
  - Expected Result: Block is hidden when `FALSE`, visible when `TRUE`.

### Automated Testing

The module includes Drupal tests under `src/Tests/` including:

- `Drupal\php\Tests\Condition\PhpConditionTest` (Kernel)
- `Drupal\Tests\php\Functional\PhpAccessTest` (Browser)
- `Drupal\Tests\php\Functional\PhpFilterTest` (Browser)
- `Drupal\Tests\php\Functional\PhpUninstallTest` (Browser)
- `Drupal\php\Tests\Plugin\views\PhpArgumentValidatorTest` (Kernel)

Run them using your project’s standard Drupal/PHPUnit test runner (the exact command varies by repo), filtering by the `@group PHP` test group where supported.
