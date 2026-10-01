# Checkbook Log

Custom logging service with file-based logging and standardized helper methods for NYC Checkbook application logging.

## Table of Contents

- [Module Purpose](#module-purpose)
- [Module Functionality](#module-functionality)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Testing](#testing)

## Module Purpose

Provides centralized logging functionality for Checkbook modules. Extends Drupal's file logging with custom formatting and convenience methods for consistent logging across the application.

Key capabilities:
- Standardized logging interface
- File-based log storage
- Multiple severity levels
- Pre-formatted output for complex data
- Service decorator pattern
- Integration with Drupal logger system

## Module Functionality

### Core Features

#### Log Levels
- **Emergency**: System unusable
- **Alert**: Action must be taken immediately
- **Critical**: Critical conditions
- **Error**: Error conditions
- **Warning**: Warning conditions
- **Notice**: Normal but significant conditions
- **Info**: Informational messages
- **Debug**: Debug-level messages

#### Helper Methods
- `log_emergency()`: Log emergency messages
- `log_alert()`: Log alert messages
- `log_critical()`: Log critical messages
- `log_error()`: Log error messages
- `log_warn()`: Log warning messages
- `log_notice()`: Log notice messages
- `log_info()`: Log info messages
- `log_debug()`: Log debug messages

#### Features
- **Pre-formatted Output**: Automatically formats arrays and objects
- **HTML Formatting**: Wraps output in `<pre><code>` tags
- **File Logging**: Writes to log files via filelog module
- **Service Decoration**: Extends default file logger

### Technical Components

#### LogHelper Class
- **LogHelper** (`src/LogHelper.php`): Static helper methods for logging
  - Provides convenience methods for all log levels
  - Formats complex data structures
  - Wraps Drupal logger service

#### Logger Service
- **CheckBookFileLog** (`src/Logger/CheckBookFileLog.php`): Custom file logger
  - Extends `FileLog` from filelog module
  - Decorates default logger service
  - Custom message rendering

#### Log Message
- **CheckBookLogMessage** (`src/CheckBookLogMessage.php`): Custom log message class

#### Service Definition
Service registered in `checkbook_log.services.yml`:
- Service ID: `logger.checkbook_log`
- Decorates: `logger.filelog`
- Tagged as: `logger`

### Log Levels Constants

```php
const LEVEL_DEBUG = 'Debug';
const LEVEL_INFO = 'Info';
const LEVEL_NOTICE = 'Notice';
const LEVEL_WARNING = 'Warning';
const LEVEL_ERROR = 'Error';
const LEVEL_CRITICAL = 'Critical';
const LEVEL_ALERT = 'Alert';
const LEVEL_EMERGENCY = 'Emergency';
```

## Installation

### Dependencies
- Drupal 10 or 11
- `filelog` module (for file-based logging)

### Installation Steps

```bash
# Enable filelog first
drush en filelog

# Enable checkbook_log
drush en checkbook_log

# Clear cache
drush cr

# Verify service
drush service:list | grep logger.checkbook_log
```

### File Permissions

Ensure log directory is writable:
```bash
chmod 755 /path/to/drupal/sites/default/files/logs
```

## Configuration

### File Log Configuration

Configure via filelog module settings:
- Log file location
- Log rotation
- File permissions
- Log format

### Log Channel

All Checkbook logs use channel: `NYC Checkbook`

### Service Decoration

Service decorates `logger.filelog`:
```yaml
services:
  logger.checkbook_log:
    class: Drupal\checkbook_log\Logger\CheckBookFileLog
    decorates: logger.filelog
    arguments:
      - '@config.factory'
      - '@state'
      - '@datetime.time'
      - '@logger.log_message_parser'
      - '@filelog.file_manager'
    tags:
      - { name: logger }
```

## Usage

### Basic Logging

```php
use Drupal\checkbook_log\LogHelper;

// Error logging
LogHelper::log_error('Database connection failed');

// Warning logging
LogHelper::log_warn('API rate limit approaching');

// Info logging
LogHelper::log_info('User logged in successfully');

// Debug logging
LogHelper::log_debug('Processing request parameters');
```

### Logging Complex Data

```php
// Log array
$data = ['user_id' => 123, 'action' => 'export'];
LogHelper::log_notice($data);

// Output:
// <pre><code>Array
// (
//     [user_id] => 123
//     [action] => export
// )
// </code></pre>

// Log object
$object = new stdClass();
$object->name = 'Test';
LogHelper::log_info($object);
```

### Critical Errors

```php
try {
  // Critical operation
  processPayment();
} catch (Exception $e) {
  LogHelper::log_critical('Payment processing failed: ' . $e->getMessage());
}
```

### Emergency Situations

```php
if ($systemDown) {
  LogHelper::log_emergency('System is down - all services unavailable');
}
```

### Alerts

```php
if ($diskSpaceLow) {
  LogHelper::log_alert('Disk space critically low - immediate action required');
}
```

### Warnings

```php
if ($cacheExpiring) {
  LogHelper::log_warn('Cache expiring soon - consider refresh');
}
```

### Debug Information

```php
if ($debugMode) {
  LogHelper::log_debug('Request params: ' . print_r($_GET, true));
}
```

### Module-Specific Logging

```php
// In checkbook_api module
LogHelper::log_notice('API request processed: ' . $requestId);

// In checkbook_datafeeds module
LogHelper::log_info('Export completed: ' . $filename);

// In checkbook_alerts module
LogHelper::log_error('Alert email failed: ' . $email);
```

## Testing

### Manual Testing

#### Scenario 1: Error Logging
- **Steps**: Call `LogHelper::log_error('Test error')`
- **Expected**: Error logged to file

#### Scenario 2: Complex Data Logging
- **Steps**: Log array with `log_notice()`
- **Expected**: Array formatted in log

#### Scenario 3: Multiple Levels
- **Steps**: Log messages at different levels
- **Expected**: All messages logged correctly

#### Scenario 4: Log File Creation
- **Steps**: Enable module, trigger log
- **Expected**: Log file created in files/logs

#### Scenario 5: Service Decoration
- **Steps**: Check service definition
- **Expected**: Service decorates filelog

#### Scenario 6: HTML Formatting
- **Steps**: Log complex data
- **Expected**: Output wrapped in pre/code tags

#### Scenario 7: Emergency Log
- **Steps**: Call `log_emergency()`
- **Expected**: Emergency level logged

#### Scenario 8: Log Rotation
- **Steps**: Generate many logs
- **Expected**: Files rotate per filelog config

#### Scenario 9: Performance
- **Steps**: Log 1000 messages
- **Expected**: No performance degradation

#### Scenario 10: Integration
- **Steps**: Use from other modules
- **Expected**: Works across all modules

### Testing Checklist
- [ ] All log levels work
- [ ] Error logging works
- [ ] Warning logging works
- [ ] Info logging works
- [ ] Debug logging works
- [ ] Notice logging works
- [ ] Critical logging works
- [ ] Alert logging works
- [ ] Emergency logging works
- [ ] Complex data formatted correctly
- [ ] Log files created
- [ ] Service decoration works
- [ ] Integration with other modules works
- [ ] Performance acceptable
- [ ] File permissions correct

### Debugging

```bash
# View logs
tail -f sites/default/files/logs/checkbook.log

# Check service
drush service:list | grep logger

# Test logging
drush php-eval "use Drupal\checkbook_log\LogHelper; LogHelper::log_notice('Test log');"

# View recent logs
drush watchdog:show --type='NYC Checkbook'

# Check log files
ls -lh sites/default/files/logs/
```

## Best Practices

1. **Use Appropriate Levels**: Choose correct severity level
2. **Meaningful Messages**: Write clear, descriptive messages
3. **Context Information**: Include relevant context
4. **Avoid Sensitive Data**: Don't log passwords, tokens, etc.
5. **Performance**: Avoid excessive debug logging in production
6. **Log Rotation**: Configure log rotation to prevent disk fill
7. **Monitoring**: Regularly review logs for errors
8. **Structured Data**: Use arrays/objects for complex data

## Related Modules

- **filelog**: Base file logging functionality
- **checkbook_project**: Core utilities
- All Checkbook modules use this for logging

## Support

For issues:
- Review `LogHelper.php` for helper methods
- Check `CheckBookFileLog.php` for custom logger
- Verify service registration in `checkbook_log.services.yml`
- Check filelog module configuration
- Review log file permissions
- Check logs directory exists and is writable
- Verify service decoration working
