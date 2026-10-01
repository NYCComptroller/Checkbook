# Checkbook Alerts

Notification system for monitoring financial data changes in the NYC Checkbook platform with email alerts and subscription management.

## Table of Contents

- [Module Purpose](#module-purpose)
- [Module Functionality](#module-functionality)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Testing](#testing)

## Module Purpose

The Checkbook Alerts module provides a comprehensive notification system for the NYC Checkbook financial transparency platform. It enables users to create custom alerts based on search criteria and receive email notifications when matching data changes or meets specified thresholds.

The module enables users to:
- Create alerts from advanced search results across all financial domains
- Set custom notification schedules (daily, weekly, monthly)
- Define result thresholds and minimum notification intervals
- Activate alerts via email confirmation
- Manage subscriptions with one-click unsubscribe
- Monitor financial data changes automatically

## Module Functionality

The module provides:

### Core Features

#### Alert Creation
- **Search-Based Alerts**: Create alerts from any advanced search query
- **Multi-Domain Support**: Alerts for Budget, Spending, Contracts, Payroll, and Revenue
- **Flexible Scheduling**: Daily, weekly, or monthly notification frequency
- **Result Thresholds**: Set minimum result count to trigger notifications
- **Minimum Days Between Alerts**: Prevent notification spam with configurable intervals
- **Expiration Dates**: Set end dates for time-limited alerts

#### Notification System
- **Email Notifications**: HTML-formatted email alerts with result summaries
- **Activation Workflow**: Two-step activation via email confirmation link
- **Unsubscribe Management**: One-click unsubscribe with token-based authentication
- **Result Tracking**: Monitors changes in result counts to trigger notifications
- **Batch Processing**: Processes all active alerts via Drush command

#### User Interface
- **Dialog-Based Form**: Alert creation in modal dialog
- **Accordion Interface**: Organized by financial domain
- **Customizable Results**: Preview and adjust alert criteria before saving
- **Schedule Configuration**: User-friendly scheduling interface
- **Alert Management**: View and manage existing alerts

### Technical Components

#### Database Schema

**`checkbook_alerts` Table**:
- `checkbook_alerts_sysid`: Primary key
- `label`: User-defined alert name
- `recipient`: Email address for notifications
- `recipient_type`: Email or Twitter (email only currently supported)
- `ref_url`: Reference URL for querying results
- `user_url`: Original page URL where alert was created
- `active`: Activation status (Y/N)
- `number_of_results`: Current result count
- `minimum_results`: Minimum results to trigger alert
- `minimum_days`: Minimum days between notifications
- `date_end`: Alert expiration date
- `date_last_new_results`: Last notification timestamp
- `domain`: Financial domain (budget, spending, contracts, payroll, revenue)
- `created_date`: Alert creation timestamp
- `active_date`: Activation timestamp
- `un_subscribed_date`: Unsubscribe timestamp

**`checkbook_alerts_sent` Table**:
- `checkbook_alerts_sent_sysid`: Primary key
- `checkbook_alerts_sysid`: Foreign key to alerts table
- `sent_date`: Timestamp of notification

#### Forms
- **CheckbookAlertsForm** (`src/Form/CheckbookAlertsForm.php`): Main alert creation form
- **CheckbookAlertsSettingForm** (`src/Form/CheckbookAlertsSettingForm.php`): Alert configuration settings

#### Controller
- **CheckbookAlertsController** (`src/Controller/CheckbookAlertsController.php`):
  - Form display
  - Alert activation
  - Unsubscribe handling
  - Transaction views

#### Services
- **CheckbookAlertsHelper** (`src/Services/CheckbookAlertsHelper.php`):
  - Alert query building
  - Data count retrieval
  - Email formatting
  - Token generation

#### Drush Commands
- **CheckbookAlertsCommands** (`src/Commands/CheckbookAlertsCommands.php`):
  - `checkbook_alerts:sendAlerts` (alias: `sendCheckbookAlerts`): Processes and sends active alerts

#### Twig Extension
- **CheckbookAlertsExtension** (`src/Twig/CheckbookAlertsExtension.php`): Custom Twig functions for alert rendering

#### Templates
- `checkbook_alerts.html.twig`: Main alert list view
- `checkbook_alerts_activate.html.twig`: Activation confirmation page
- `checkbook_alerts_activated.html.twig`: Post-activation success message
- `checkbook_alerts_unsubscribe.html.twig`: Unsubscribe confirmation page
- `checkbook_alerts_email.html.twig`: Email notification template
- `checkbook_alerts_advanced_search.html.twig`: Advanced search alert form
- `checkbook_alerts_advanced_search_confirm.html.twig`: Alert creation confirmation
- `checkbook-alerts-subscribe.html.twig`: Subscription management

### Alert Workflow

1. **Creation**: User performs search and clicks "Create Alert"
2. **Customization**: User configures alert schedule and thresholds
3. **Submission**: Alert saved to database with `active='N'`
4. **Activation Email**: System sends email with activation link
5. **Activation**: User clicks link, alert status changes to `active='Y'`
6. **Monitoring**: Drush cron job checks active alerts
7. **Notification**: When criteria met, email sent with results
8. **Tracking**: Result count and last notification date updated
9. **Unsubscribe**: User can unsubscribe via email link

## Installation

### Dependencies

- Drupal 10 or 11
- `checkbook_project` module (required)
- `checkbook_advanced_search` module (for search-based alerts)
- `checkbook_smart_search` module (for quick search alerts)
- `checkbook_infrastructure_layer` module (for utilities)
- `checkbook_log` module (for logging)

### Installation Steps

1. Ensure all dependencies are installed and enabled
2. Enable the module:
   ```bash
   drush en checkbook_alerts
   ```
3. Run database updates to create tables:
   ```bash
   drush updb
   ```
4. Clear cache:
   ```bash
   drush cr
   ```
5. Configure cron to run the alert processing command:
   ```bash
   # Add to crontab (example: run every hour)
   0 * * * * cd /path/to/drupal && drush checkbook_alerts:sendAlerts
   ```

### Database Tables

The module creates two tables on installation:

- **checkbook_alerts**: Stores alert configurations
- **checkbook_alerts_sent**: Logs sent notifications

## Configuration

### Email Configuration

Ensure Drupal's email system is properly configured:

1. **Site Email**: Set in Configuration > System > Site Information
2. **Mail System**: Configure in Configuration > System > Mail System
3. **SMTP** (recommended): Use SMTP module for reliable email delivery

### Cron Configuration

Alert processing requires regular cron execution:

```bash
# System crontab entry
0 * * * * cd /var/www/html/checkbook && drush checkbook_alerts:sendAlerts >> /var/log/checkbook-alerts.log 2>&1
```

**Recommended Frequency**: Every 1-4 hours depending on alert volume

### Alert Settings

Configure default settings in `CheckbookAlertsSettingForm`:
- Default minimum results threshold
- Default minimum days between notifications
- Maximum alert duration
- Email template customization

### Routes

The module defines the following routes:

- **Alert Form**: `/checkbook-alerts`
- **Alert Transactions**: `/alert/transactions`
- **Alert Transactions Form**: `/alert/transactions/form`
- **Advanced Search Alert Form**: `/alert/transactions/advanced/search/form`
- **Activate Alert**: `/alert/activate/{token}`
- **Unsubscribe**: `/alert/unsubscribe/{token}`

### Token Security

Alert activation and unsubscribe links use secure tokens:
- Tokens are unique per alert
- Tokens are generated using `checkbook_alerts_sysid` and recipient
- Tokens prevent unauthorized access to alert management

## Usage

### Creating an Alert from Advanced Search

1. **Perform Search**:
   - Navigate to `/advanced-search`
   - Select domain (Budget, Spending, Contracts, Payroll, or Revenue)
   - Enter search criteria
   - Click "Search"

2. **Create Alert**:
   - Review search results
   - Click "Create Alert" button
   - Alert creation dialog opens

3. **Configure Alert**:
   - **Alert Label**: Enter descriptive name
   - **Email Address**: Enter notification email
   - **Schedule**: Select frequency (Daily, Weekly, Monthly)
   - **Minimum Results**: Set threshold for notifications
   - **Minimum Days**: Set minimum interval between notifications
   - **End Date**: Set alert expiration date

4. **Submit**:
   - Click "Create Alert"
   - Confirmation message displays
   - Activation email sent to specified address

5. **Activate**:
   - Check email for activation link
   - Click activation link
   - Alert status changes to active
   - Monitoring begins

### Managing Alerts

#### View Alerts
Navigate to `/alert/transactions` to view all alerts for your email address.

#### Activate Alert
Click the activation link in the confirmation email. The alert will become active and begin monitoring.

#### Unsubscribe from Alert
Click the unsubscribe link in any notification email. The alert will be deactivated and no further notifications will be sent.

### Alert Notification Email

Notification emails include:
- Alert label and description
- Current result count
- Change from previous count
- Link to view full results
- Unsubscribe link

### Processing Alerts (Drush Command)

Manually process alerts:

```bash
# Process all active alerts
drush checkbook_alerts:sendAlerts

# Or use alias
drush sendCheckbookAlerts
```

The command:
1. Queries all active alerts
2. Checks if minimum days have passed since last notification
3. Executes search query to get current result count
4. Compares with previous result count
5. Sends email if threshold met
6. Updates alert record with new count and timestamp
7. Logs notification in `checkbook_alerts_sent` table

### Alert Criteria

Alerts can be created based on:
- **Budget**: Fiscal year, agency, department, expense category, budget code, amount ranges
- **Spending**: Vendor, agency, fiscal year, amount ranges, M/WBE status, industry type
- **Contracts**: Contract type, status, vendor, agency, award method, date ranges, M/WBE status
- **Payroll**: Employee name, title, agency, salary range, fiscal year
- **Revenue**: Revenue category, source, agency, fiscal year, amount ranges, fund class

### Integration with Advanced Search

The module integrates seamlessly with `checkbook_advanced_search`:
- Alert button appears in search results
- Search criteria automatically captured
- Reference URL stored for result monitoring
- User URL stored for context

## Testing

### Manual Testing

#### Scenario 1: Alert Creation
- **Description**: Verify alert can be created from search results
- **Steps**:
  1. Navigate to `/advanced-search`
  2. Perform a search in any domain
  3. Click "Create Alert" button
  4. Fill in alert configuration form
  5. Submit form
  6. Check email for activation link
- **Expected Result**: Alert created successfully, activation email received

#### Scenario 2: Alert Activation
- **Description**: Test alert activation workflow
- **Steps**:
  1. Create an alert (see Scenario 1)
  2. Open activation email
  3. Click activation link
  4. Verify activation confirmation page displays
  5. Check database: `active` field should be 'Y'
- **Expected Result**: Alert activated successfully, status updated in database

#### Scenario 3: Alert Notification
- **Description**: Verify alerts are sent when criteria met
- **Steps**:
  1. Create and activate an alert with low threshold (minimum_results=1)
  2. Set minimum_days=0 for immediate testing
  3. Run Drush command: `drush sendCheckbookAlerts`
  4. Check email for notification
  5. Verify notification includes result count and unsubscribe link
- **Expected Result**: Notification email received with correct data

#### Scenario 4: Result Threshold
- **Description**: Test minimum results threshold
- **Steps**:
  1. Create alert with minimum_results=10
  2. Ensure search returns fewer than 10 results
  3. Run Drush command
  4. Verify no email sent
  5. Modify data to return 10+ results
  6. Run Drush command again
  7. Verify email sent
- **Expected Result**: Email only sent when result count meets threshold

#### Scenario 5: Minimum Days Between Notifications
- **Description**: Test notification frequency limiting
- **Steps**:
  1. Create alert with minimum_days=7
  2. Activate alert
  3. Run Drush command (email sent)
  4. Run Drush command again immediately
  5. Verify no second email sent
  6. Wait 7 days (or modify database timestamp)
  7. Run Drush command
  8. Verify email sent
- **Expected Result**: Emails only sent after minimum days interval

#### Scenario 6: Unsubscribe
- **Description**: Test unsubscribe functionality
- **Steps**:
  1. Create and activate an alert
  2. Receive notification email
  3. Click unsubscribe link in email
  4. Verify unsubscribe confirmation page
  5. Check database: `un_subscribed_date` should be set
  6. Run Drush command
  7. Verify no further emails sent
- **Expected Result**: Alert unsubscribed successfully, no further notifications

#### Scenario 7: Alert Expiration
- **Description**: Test alert expiration date
- **Steps**:
  1. Create alert with date_end set to tomorrow
  2. Activate alert
  3. Run Drush command (email sent)
  4. Wait until after expiration date
  5. Run Drush command
  6. Verify no email sent
- **Expected Result**: Expired alerts do not send notifications

#### Scenario 8: Multiple Alerts per User
- **Description**: Test multiple alerts for same email
- **Steps**:
  1. Create 3 different alerts with same email address
  2. Activate all alerts
  3. Run Drush command
  4. Verify single email received with all 3 alerts
  5. Check that each alert is listed separately
- **Expected Result**: All active alerts for user included in single notification

#### Scenario 9: Invalid Email Address
- **Description**: Test form validation for email
- **Steps**:
  1. Create alert with invalid email (e.g., "notanemail")
  2. Submit form
  3. Verify validation error displayed
  4. Enter valid email
  5. Submit form
  6. Verify alert created
- **Expected Result**: Invalid emails rejected, valid emails accepted

#### Scenario 10: Drush Command Logging
- **Description**: Verify Drush command logs activity
- **Steps**:
  1. Enable checkbook_log module
  2. Run `drush sendCheckbookAlerts`
  3. Check logs for processing messages
  4. Verify log includes:
     - Number of recipients processed
     - Number of alerts checked
     - Result counts for each alert
     - Emails sent
- **Expected Result**: Detailed logging of alert processing

### Automated Testing

Currently, the module relies on manual testing. Future automated testing could include:

- **PHPUnit Tests**:
  - Alert creation logic
  - Token generation and validation
  - Result count comparison
  - Email formatting

- **Functional Tests**:
  - Alert form submission
  - Activation workflow
  - Unsubscribe workflow
  - Drush command execution

- **Integration Tests**:
  - Database operations
  - Email sending
  - Search query execution
  - Cron job processing

- **Performance Tests**:
  - Large number of alerts
  - Complex search queries
  - Email batch processing

### Testing Checklist

- [ ] Alert creation form displays correctly
- [ ] Form validation works for all fields
- [ ] Activation email sent successfully
- [ ] Activation link works and updates database
- [ ] Drush command processes alerts without errors
- [ ] Notification emails sent when criteria met
- [ ] Result threshold respected
- [ ] Minimum days interval enforced
- [ ] Unsubscribe link works correctly
- [ ] Expired alerts do not send notifications
- [ ] Multiple alerts per user handled correctly
- [ ] Database records created and updated properly
- [ ] Logging captures all relevant events
- [ ] Email templates render correctly
- [ ] Token security prevents unauthorized access

### Debugging

To debug alert issues:

1. **Check Database**:
   ```sql
   -- View all alerts
   SELECT * FROM checkbook_alerts;
   
   -- View sent notifications
   SELECT * FROM checkbook_alerts_sent;
   
   -- Check active alerts
   SELECT * FROM checkbook_alerts WHERE active='Y' AND un_subscribed_date IS NULL;
   ```

2. **Enable Logging**:
   - Check watchdog logs: `/admin/reports/dblog`
   - Review checkbook_log entries
   - Monitor Drush command output

3. **Test Email**:
   - Verify site email configuration
   - Test with simple email module
   - Check spam folders

4. **Verify Cron**:
   ```bash
   # Check cron is running
   crontab -l
   
   # Test manual execution
   drush sendCheckbookAlerts
   ```

5. **Inspect Queries**:
   - Enable query logging
   - Review generated SQL
   - Test ref_url directly

## Best Practices

1. **Set Reasonable Thresholds**: Avoid setting minimum_results too low to prevent notification spam
2. **Use Descriptive Labels**: Help users identify alerts easily
3. **Monitor Alert Volume**: Track number of active alerts and notification frequency
4. **Regular Cleanup**: Periodically remove expired or unsubscribed alerts
5. **Test Before Production**: Always test alert creation and notification in staging
6. **Optimize Queries**: Ensure ref_url queries are performant
7. **Email Deliverability**: Use SMTP module for reliable email delivery
8. **Log Monitoring**: Regularly review logs for errors or issues
9. **User Communication**: Provide clear instructions in activation and notification emails
10. **Security**: Never expose alert tokens or allow token guessing

## Related Modules

- **checkbook_advanced_search**: Primary source of alert criteria
- **checkbook_smart_search**: Quick search integration for alerts
- **checkbook_project**: Core utilities and data access
- **checkbook_infrastructure_layer**: Common constants and utilities
- **checkbook_log**: Logging and monitoring
- **checkbook_datafeeds**: Export functionality for alert results

## Support

For issues or questions about the alerts module:
- Review database schema in `checkbook_alerts.install`
- Check Drush command implementation in `src/Commands/CheckbookAlertsCommands.php`
- Review email templates in `templates/` directory
- Check logs for processing errors
- Consult the Checkbook NYC development team
- Refer to this README for usage guidelines
