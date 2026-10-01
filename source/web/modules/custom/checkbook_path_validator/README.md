# Checkbook Path Validator

Security module for validating URL paths and query parameters using regex patterns to prevent malicious input and injection attacks.

## Table of Contents

- [Module Purpose](#module-purpose)
- [Module Functionality](#module-functionality)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Testing](#testing)

## Module Purpose

Provides inbound path processing to validate URL paths and query parameters against configurable regex patterns. Returns 404 for invalid requests to prevent SQL injection, XSS, and other attacks.

Key capabilities:
- Path parameter validation
- Query parameter validation
- Regex-based pattern matching
- Configurable validation rules
- 404 response for invalid input
- High-priority path processing (999)
- Admin configuration interface

## Module Functionality

### Core Features

#### Path Validation
- **Path Elements**: Validates URL path segments
- **Pattern Matching**: Uses regex patterns for validation
- **Parameter-Specific**: Different rules per parameter
- **404 on Failure**: Returns 404 for invalid paths

#### Query Validation
- **Query Parameters**: Validates URL query strings
- **Same Patterns**: Uses same regex as path validation
- **Multiple Parameters**: Validates all query params
- **Security Focus**: Prevents injection attacks

#### Configuration
- **Admin Interface**: Configure validation rules
- **Enable/Disable**: Toggle validation on/off
- **Custom Patterns**: Define regex patterns per parameter
- **Regex Delimiter**: Configurable delimiter

### Technical Components

#### Path Processor
- **CheckbookPathProcessor** (`src/PathProcessor/CheckbookPathProcessor.php`):
  - Implements `InboundPathProcessorInterface`
  - Priority: 999 (runs early)
  - Validates paths and queries
  - Throws `NotFoundHttpException` on failure

#### Configuration Form
- **PathSettingsForm** (`src/Form/PathSettingsForm.php`): Admin configuration interface

#### Service Registration
Service registered in `checkbook_path_validator.services.yml`:
- Service ID: `checkbook_path_validator.path_processor`
- Tag: `path_processor_inbound` with priority 999

#### Configuration Storage
Settings stored in: `checkbook_path_validator.settings`

Configuration structure:
```php
[
  'status' => TRUE,  // Enable/disable
  'regex_delimiter' => '/',  // Regex delimiter
  'items' => [
    [
      'parameter' => 'year',
      'regex' => '^[0-9]{4}$'  // 4-digit year
    ],
    [
      'parameter' => 'agency',
      'regex' => '^[0-9]{3}$'  // 3-digit agency code
    ]
  ]
]
```

### Validation Process

1. **Request Received**: Inbound request processed
2. **Check Status**: If validation enabled
3. **Parse Path**: Split path into elements
4. **Parse Query**: Get query parameters
5. **Match Parameters**: Find matching validation rules
6. **Apply Regex**: Test against pattern
7. **Pass/Fail**: Continue or throw 404

### Example Validation

**Valid Path**:
```
/budget/year/2024/agency/057
```
- `year`: Matches `^[0-9]{4}$` ✓
- `agency`: Matches `^[0-9]{3}$` ✓

**Invalid Path**:
```
/budget/year/2024abc/agency/057
```
- `year`: Fails `^[0-9]{4}$` ✗
- Returns 404

**Valid Query**:
```
/spending?year=2024&agency=057
```
- `year`: Matches pattern ✓
- `agency`: Matches pattern ✓

**Invalid Query**:
```
/spending?year=2024&agency=<script>
```
- `agency`: Fails pattern ✗
- Returns 404

## Installation

### Dependencies
- Drupal 10 or 11

### Installation Steps

```bash
# Enable module
drush en checkbook_path_validator

# Clear cache
drush cr

# Configure validation rules
# Visit /admin/config/system/path-settings
```

## Configuration

### Admin Configuration

Navigate to: `/admin/config/system/path-settings`

**Configuration Options**:
- **Enable Validation**: Toggle validation on/off
- **Regex Delimiter**: Set delimiter (default: `/`)
- **Validation Rules**: Add parameter/regex pairs

### Adding Validation Rules

**Example Rules**:

```php
// Fiscal Year (4 digits)
Parameter: year
Regex: ^[0-9]{4}$

// Agency Code (3 digits)
Parameter: agency
Regex: ^[0-9]{3}$

// Vendor ID (numeric)
Parameter: vendor
Regex: ^[0-9]+$

// Amount (numeric with optional decimals)
Parameter: amount
Regex: ^[0-9]+(\.[0-9]{2})?$

// Alphanumeric (letters and numbers only)
Parameter: code
Regex: ^[a-zA-Z0-9]+$

// Safe string (no special chars)
Parameter: name
Regex: ^[a-zA-Z0-9\s\-_]+$
```

### Common Patterns

**Numeric Only**:
```regex
^[0-9]+$
```

**Alphanumeric**:
```regex
^[a-zA-Z0-9]+$
```

**Date (YYYY-MM-DD)**:
```regex
^[0-9]{4}-[0-9]{2}-[0-9]{2}$
```

**Email**:
```regex
^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$
```

**Safe String (no HTML/SQL)**:
```regex
^[a-zA-Z0-9\s\-_.,]+$
```

### Priority Setting

Path processor runs with priority **999** (very early):
```yaml
tags:
  - { name: path_processor_inbound, priority: 999 }
```

This ensures validation happens before other processing.

## Usage

### Enabling Validation

1. **Navigate**: Go to `/admin/config/system/path-settings`
2. **Enable**: Check "Enable Validation"
3. **Add Rules**: Configure parameter patterns
4. **Save**: Save configuration

### Configuring Rules

```php
// Via configuration form or programmatically
$config = \Drupal::configFactory()->getEditable('checkbook_path_validator.settings');
$config->set('status', TRUE);
$config->set('regex_delimiter', '/');
$config->set('items', [
  [
    'parameter' => 'year',
    'regex' => '^[0-9]{4}$'
  ],
  [
    'parameter' => 'agency',
    'regex' => '^[0-9]{3}$'
  ]
]);
$config->save();
```

### Testing Validation

**Valid Request**:
```bash
curl http://localhost/budget/year/2024
# Returns: 200 OK
```

**Invalid Request**:
```bash
curl http://localhost/budget/year/2024abc
# Returns: 404 Not Found
```

### Disabling Validation

Temporarily disable:
```php
$config = \Drupal::configFactory()->getEditable('checkbook_path_validator.settings');
$config->set('status', FALSE);
$config->save();
```

### Debugging Validation

```php
// Check current config
$config = \Drupal::config('checkbook_path_validator.settings')->get();
print_r($config);

// Test pattern
$pattern = '/^[0-9]{4}$/';
$value = '2024';
if (preg_match($pattern, $value)) {
  echo "Valid";
} else {
  echo "Invalid";
}
```

## Testing

### Manual Testing

#### Scenario 1: Valid Path
- **Steps**: Request `/budget/year/2024`
- **Expected**: Page loads normally

#### Scenario 2: Invalid Path
- **Steps**: Request `/budget/year/abc123`
- **Expected**: 404 error

#### Scenario 3: Valid Query
- **Steps**: Request `/spending?year=2024`
- **Expected**: Page loads normally

#### Scenario 4: Invalid Query
- **Steps**: Request `/spending?year=<script>`
- **Expected**: 404 error

#### Scenario 5: Multiple Parameters
- **Steps**: Request `/budget/year/2024/agency/057`
- **Expected**: Both validated, page loads

#### Scenario 6: SQL Injection Attempt
- **Steps**: Request `/spending?vendor=1' OR '1'='1`
- **Expected**: 404 error

#### Scenario 7: XSS Attempt
- **Steps**: Request `/search?q=<script>alert(1)</script>`
- **Expected**: 404 error

#### Scenario 8: Disabled Validation
- **Steps**: Disable validation, request invalid path
- **Expected**: Page loads (validation bypassed)

#### Scenario 9: Configuration Update
- **Steps**: Add new validation rule
- **Expected**: New rule enforced immediately

#### Scenario 10: Edge Cases
- **Steps**: Test empty values, special chars
- **Expected**: Handled correctly per rules

### Testing Checklist
- [ ] Valid paths pass
- [ ] Invalid paths return 404
- [ ] Valid queries pass
- [ ] Invalid queries return 404
- [ ] Multiple parameters validated
- [ ] SQL injection blocked
- [ ] XSS attempts blocked
- [ ] Configuration saves correctly
- [ ] Enable/disable works
- [ ] Priority 999 respected
- [ ] No false positives
- [ ] No false negatives
- [ ] Performance acceptable
- [ ] Regex patterns correct
- [ ] Error handling works

### Debugging

```bash
# Check configuration
drush config:get checkbook_path_validator.settings

# Test path processor
drush php-eval "print_r(\Drupal::service('checkbook_path_validator.path_processor'));"

# Check logs
drush watchdog:show

# Test regex
drush php-eval "echo preg_match('/^[0-9]{4}$/', '2024') ? 'Match' : 'No match';"

# Disable validation
drush config:set checkbook_path_validator.settings status 0
```

## Best Practices

1. **Strict Patterns**: Use strict regex patterns
2. **Whitelist Approach**: Allow only known-good input
3. **Test Thoroughly**: Test all validation rules
4. **Monitor Logs**: Watch for blocked requests
5. **Update Patterns**: Keep patterns current
6. **Performance**: Use efficient regex
7. **Documentation**: Document all validation rules
8. **Security**: Treat as critical security layer

## Related Modules

- **checkbook_project**: Core functionality
- **checkbook_infrastructure_layer**: Utilities

## Support

For issues:
- Review path processor in `src/PathProcessor/CheckbookPathProcessor.php`
- Check configuration form in `src/Form/PathSettingsForm.php`
- Verify configuration at `/admin/config/system/path-settings`
- Test regex patterns
- Check service registration
- Review priority setting
- Monitor 404 errors
- Validate patterns against expected input
