# Checkbook ETL Notification

Automated daily email notifications for ETL (Extract, Transform, Load) process status monitoring across Production and UAT environments.

## Table of Contents

- [Module Purpose](#module-purpose)
- [Module Functionality](#module-functionality)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Testing](#testing)

## Module Purpose

Monitors ETL data pipeline health and sends daily status emails to administrators. Tracks data processing success/failure, file processing status, shard refreshes, and Solr index updates.

Key capabilities:
- Daily automated ETL status emails
- Production and UAT environment monitoring
- Success/failure detection
- High-priority alerts for failures
- HTML-formatted status reports
- Detailed statistics tables
- Error tracking and reporting

## Module Functionality

### Core Features

#### ETL Monitoring
- **Production Status**: Monitors prod ETL processes
- **UAT Status**: Monitors UAT ETL processes
- **Success Detection**: Checks if ETL ran within last 12 hours
- **Failure Detection**: Identifies failed runs
- **File Processing**: Tracks if all files processed
- **Shard Refresh**: Monitors database shard updates
- **Solr Refresh**: Tracks search index updates
- **Error Counts**: Reports process error counts

#### Email Notifications
- **Daily Emails**: Sent every morning via cron
- **HTML Format**: Formatted status tables
- **Priority Headers**: High priority for failures
- **Dual Environment**: Prod and UAT in single email
- **Conditional Content**: Different content for success/failure
- **Error Highlighting**: Visual indicators for issues

#### Status Checks
- **Last Run Date**: When ETL last executed
- **Last Success Date**: When ETL last succeeded
- **Last Load Date**: When data last loaded successfully
- **Shard Status**: Database shard refresh flag
- **Index Status**: Solr index refresh flag
- **Abort Flag**: Process abortion detection
- **Error Count**: Number of processing errors

### Technical Components

#### Core Class
- **CheckbookEtlStatistics** (`src/Includes/CheckbookEtlStatistics.php`):
  - Queries ETL status tables
  - Gathers statistics
  - Formats email data
  - Determines success/failure
  - Sends notifications

#### Hooks
- **hook_cron()**: Triggers daily status check
- **hook_mail()**: Formats email message
- **hook_mail_alter()**: Adds priority headers, renders HTML template
- **hook_theme()**: Registers email template
- **Preprocess function**: Processes template variables

#### Template
- **etl-status.email.html.twig** (`templates/etl-status.email.html.twig`): HTML email template with status tables

#### Twig Extension
- **CheckbookEtlNotificationExtension** (`src/Twig/CheckbookEtlNotificationExtension.php`): Custom Twig functions

#### Constants
- **SUCCESS_IF_RUN_LESS_THAN_X_SECONDS_AGO**: 43200 (12 hours)
- **CRON_LAST_RUN_DRUPAL_VAR**: `checkbook_etl_status_last_run`

### Status Logic

**Success Criteria**:
- ETL ran within last 12 hours
- `success_yn = 'Y'`
- Last success date > yesterday 9pm

**Failure Criteria**:
- ETL didn't run in last 12 hours
- `success_yn = 'N'`
- Last success date < yesterday 9pm

**Process Errors**:
- All files not processed (`process_abort_flag_yn = 'N'`)
- Shards refreshed (`shard_refresh_flag_yn = 'Y'`)
- Solr refreshed (`index_refresh_flag_yn = 'Y'`)

### Email Priority

High priority headers added when:
- Subject contains "Fail"
- Subject contains "not Found"

Headers:
```
X-Priority: 1 (Highest)
X-MSMail-Priority: High
Importance: High
```

## Installation

### Dependencies
- Drupal 10 or 11
- `checkbook_log` module (for logging)
- Database access to ETL status tables

### Installation Steps

```bash
# Enable module
drush en checkbook_etl_notification

# Clear cache
drush cr

# Verify cron configured
drush cron
```

### Cron Setup

Module uses Drupal cron:
```bash
# Ensure cron runs daily (e.g., 6am)
0 6 * * * cd /var/www/html && drush cron
```

## Configuration

### Email Recipients

Configure in `CheckbookEtlStatistics.php`:
```php
// Set recipients
$recipients = ['admin@example.com', 'devops@example.com'];
```

Or via configuration management.

### Database Connection

ETL status tables must be accessible:
- `etl_status` table (or similar)
- Contains fields:
  - `database_name`
  - `host_environment`
  - `last_success_date`
  - `success_yn`
  - `last_successful_load_date`
  - `shard_refresh_flag_yn`
  - `index_refresh_flag_yn`
  - `process_abort_flag_yn`
  - `process_error_count`

### Success Threshold

Modify in `CheckbookEtlStatistics.php`:
```php
// Default: 12 hours
const SUCCESS_IF_RUN_LESS_THAN_X_SECONDS_AGO = 60 * 60 * 12;

// Change to 6 hours:
const SUCCESS_IF_RUN_LESS_THAN_X_SECONDS_AGO = 60 * 60 * 6;
```

### Email Template

Customize `templates/etl-status.email.html.twig`:
- Modify HTML structure
- Change styling
- Add/remove fields
- Adjust formatting

## Usage

### Automatic Daily Emails

Emails sent automatically via cron:
1. Cron runs (e.g., 6am daily)
2. Module queries ETL status tables
3. Gathers prod and UAT statistics
4. Determines success/failure
5. Renders HTML email
6. Sends to configured recipients

### Manual Trigger

```bash
# Trigger cron manually
drush cron

# Or call function directly
drush php-eval "checkbook_etl_notification_cron();"
```

### Email Content

**Success Email**:
```
Subject: ETL Status - Success

Production Status: Success
- Last Run: 2024-01-15 21:00:00
- Last Success: 2024-01-15 21:30:00
- Files Processed: Y
- Shards Refreshed: Y
- Solr Refreshed: Y
- Errors: 0

UAT Status: Success
- Last Run: 2024-01-15 21:00:00
- Last Success: 2024-01-15 21:30:00
- Files Processed: Y
- Shards Refreshed: Y
- Solr Refreshed: Y
- Errors: 0
```

**Failure Email** (High Priority):
```
Subject: ETL Status - Fail

Production Status: Fail
- Last Run: 2024-01-14 21:00:00
- Last Success: 2024-01-14 09:00:00
- Files Processed: N
- Shards Refreshed: Y
- Solr Refreshed: Y
- Errors: 5

UAT Status: Success
- Last Run: 2024-01-15 21:00:00
- Last Success: 2024-01-15 21:30:00
- Files Processed: Y
- Shards Refreshed: Y
- Solr Refreshed: Y
- Errors: 0
```

### Status Interpretation

**All Green**:
- Both prod and UAT successful
- All files processed
- Shards and Solr refreshed
- No errors

**Process Errors**:
- Files not processed
- But shards and Solr refreshed
- Indicates partial failure

**Complete Failure**:
- ETL didn't run
- Or ran but failed
- Last success > 12 hours ago

## Testing

### Manual Testing

#### Scenario 1: Success Email
- **Steps**: Ensure ETL ran successfully, trigger cron
- **Expected**: Success email with green status

#### Scenario 2: Failure Email
- **Steps**: Simulate ETL failure, trigger cron
- **Expected**: High-priority failure email

#### Scenario 3: Prod Fail, UAT Success
- **Steps**: Prod ETL fails, UAT succeeds
- **Expected**: Email shows mixed status

#### Scenario 4: Process Errors
- **Steps**: Files not processed but shards refreshed
- **Expected**: Process error flag set in email

#### Scenario 5: Email Priority
- **Steps**: Trigger failure scenario
- **Expected**: Email has high-priority headers

#### Scenario 6: HTML Formatting
- **Steps**: Receive email, check rendering
- **Expected**: HTML table displays correctly

#### Scenario 7: Cron Timing
- **Steps**: Run cron at different times
- **Expected**: Email sent only once per day

#### Scenario 8: Database Query
- **Steps**: Check ETL status table data
- **Expected**: Accurate data in email

#### Scenario 9: Multiple Recipients
- **Steps**: Configure multiple emails
- **Expected**: All recipients receive email

#### Scenario 10: Template Rendering
- **Steps**: Modify template, trigger email
- **Expected**: Changes reflected in email

### Testing Checklist
- [ ] Cron triggers email
- [ ] Success status detected correctly
- [ ] Failure status detected correctly
- [ ] High-priority headers on failures
- [ ] HTML email renders properly
- [ ] Prod status accurate
- [ ] UAT status accurate
- [ ] Process errors detected
- [ ] Error counts displayed
- [ ] All recipients receive email
- [ ] Email sent once per day
- [ ] Template variables populate
- [ ] Twig functions work
- [ ] Database queries execute
- [ ] Logs created properly

### Debugging

```bash
# Check cron last run
drush state:get checkbook_etl_status_last_run

# View logs
drush watchdog:show --type=checkbook_etl_notification

# Test database query
drush sql:query "SELECT * FROM etl_status LIMIT 5"

# Trigger manually
drush php-eval "checkbook_etl_notification_cron();"

# Check email queue
drush queue:list | grep mail

# View template
cat templates/etl-status.email.html.twig
```

## Best Practices

1. **Monitor Daily**: Check emails every morning
2. **Act on Failures**: Investigate failures immediately
3. **Tune Threshold**: Adjust 12-hour window if needed
4. **Test Recipients**: Verify all recipients receive emails
5. **Update Template**: Keep template current with needs
6. **Log Review**: Regularly check logs for issues
7. **Database Health**: Ensure ETL status table updated
8. **Email Deliverability**: Use SMTP for reliable delivery

## Related Modules

- **checkbook_log**: Logging functionality
- **checkbook_project**: Core utilities

## Support

For issues:
- Review `CheckbookEtlStatistics.php` for query logic
- Check ETL status table structure
- Verify cron running daily
- Test email configuration
- Review template rendering
- Check database connectivity
- Monitor logs for errors
