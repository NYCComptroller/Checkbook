# Checkbook Data Feeds

User-facing data export system with form-based filtering, queue processing, and downloadable CSV/XML files for bulk financial data.

## Table of Contents

- [Module Purpose](#module-purpose)
- [Module Functionality](#module-functionality)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Testing](#testing)

## Module Purpose

Provides web forms for users to request custom data exports from NYC Checkbook. Handles large dataset requests via queue system with email delivery of downloadable files.

Key capabilities:
- Form-based data export requests
- Multi-domain support (Budget, Spending, Contracts, Payroll, Revenue)
- Multi-datasource (Citywide, NYCHA, EDC/OGE)
- Queue processing for large datasets
- CSV and XML export formats
- Email delivery with download links
- Request tracking with tokens
- Dynamic form field dependencies

## Module Functionality

### Core Features

#### Export Forms
- **Budget Form**: Fiscal year, agency, department, expense category, budget code filters
- **Spending Form**: Vendor, agency, department, expense category, fiscal year, amount ranges
- **Contracts Form**: Contract type, status, vendor, agency, award method, date ranges
- **Payroll Form**: Agency, employee title, salary ranges, fiscal year
- **Revenue Form**: Revenue category, source, fund class, fiscal year, amount ranges

#### Queue System
- **Async Processing**: Large requests processed in background
- **Token Generation**: Unique tracking tokens for each request
- **Email Notification**: Download link sent when ready
- **Status Tracking**: Check request status via tracking page
- **File Storage**: ZIP files with CSV/XML data

#### Data Export
- **CSV Format**: Comma-separated values with headers
- **XML Format**: Structured XML output
- **Large Datasets**: Handles millions of records
- **Compression**: ZIP files for download
- **Expiration**: Files auto-delete after period

### Technical Components

#### Forms

**Main Forms**:
- **CheckbookDatafeedForm** (`src/Form/CheckbookDatafeedForm.php`): Base datafeed form
- **CheckbookDatafeedsTrackingForm** (`src/Form/CheckbookDatafeedsTrackingForm.php`): Track request status
- **CheckbookDatafeedsTrackingStatusForm** (`src/Form/CheckbookDatafeedsTrackingStatusForm.php`): Display status

**Domain Forms** (in `src/Form/[Domain]/`):
- Budget form
- Spending form
- Contracts form
- Payroll form
- Revenue form

#### Utilities

**Form Utilities**:
- **FormUtil** (`src/Utilities/FormUtil.php`): Agency, department, expense category options
- **BudgetFormUtil** (`src/Budget/BudgetFormUtil.php`): Budget-specific options
- **DatafeedsConfigUtil** (`src/DatafeedsConfigUtil.php`): Configuration management

**Domain Utilities** (in `src/[Domain]/`):
- Budget utilities
- Spending utilities
- Contracts utilities
- Payroll utilities
- Revenue utilities

#### Controller
- **DefaultController** (`src/Controller/DefaultController.php`):
  - Data feeds page
  - API documentation page
  - Tracking results page
  - Download handler

#### Integration
- **CheckBookAPI**: Uses checkbook_api for data retrieval
- **QueueUtil**: Queue management
- **SearchCriteria**: Request criteria handling
- **QueueCriteria**: Queue-specific criteria

#### JavaScript
16 JS files for:
- Form interactions
- Dynamic field updates
- AJAX submissions
- Validation
- Domain-specific logic

#### Configuration
JSON config files in `config/`:
- Domain-specific configurations
- Field definitions
- Validation rules

## Installation

### Dependencies
- Drupal 10 or 11
- `checkbook_api` module (for data export)
- `checkbook_infrastructure_layer` module (constants, utilities)
- `checkbook_log` module (logging)
- `data_controller` module (data access)

### Installation Steps

```bash
# Enable module
drush en checkbook_datafeeds

# Clear cache
drush cr

# Verify forms accessible
# Visit /data-feeds
```

### Queue Setup

```bash
# Add cron for queue processing
0 * * * * cd /var/www/html && drush queue:run checkbook_datafeeds_queue
```

## Configuration

### Routes

- **Data Feeds Home**: `/data-feeds`
- **API Documentation**: `/data-feeds/api`
- **Track Request**: `/track-data-feed`
- **Download File**: `/data-feeds/download/{token}`

### Form Configuration

Forms configured via:
- JSON config files in `config/`
- Form classes in `src/Form/`
- Utility classes for dynamic options

### AJAX Endpoints

Dynamic field population:
- `/datafeeds/budget/department/{year}/{agency}/{feeds}`
- `/datafeeds/expcat/{domain}/{year}/{agency}/{dept}`
- `/datafeeds/spending/agency/{data_source}/{json_output}`
- `/datafeeds/spending/department/{year}/{agency}/{spending_cat}/{data_source}/{feeds}`
- `/datafeeds/spending/expcategory/{year}/{agency}/{dept}/{spending_cat}/{data_source}/{feeds}`
- `/data-feeds/budget_type/{domain}/{dataSource}/{budgetName}/{json}`
- `/data-feeds/budget_name/{domain}/{dataSource}/{budgetType}/{json}`

### File Storage

Export files stored in: `public://checkbook_datafeeds/`

Default: `/sites/default/files/checkbook_datafeeds/`

### Email Configuration

Ensure Drupal mail system configured for delivery notifications.

## Usage

### Requesting Data Export

1. **Navigate**: Go to `/data-feeds`
2. **Select Domain**: Choose Budget, Spending, Contracts, Payroll, or Revenue
3. **Select Datasource**: Citywide, NYCHA, or EDC/OGE
4. **Apply Filters**: Select fiscal year, agency, and other criteria
5. **Choose Format**: CSV or XML
6. **Enter Email**: Provide email for notification
7. **Submit**: Click "Submit" button

### Example: Budget Export

```
1. Visit /data-feeds
2. Select "Budget"
3. Select datasource: "Citywide"
4. Fiscal Year: 2024
5. Agency: Police Department (057)
6. Department: (optional)
7. Expense Category: Personal Services
8. Format: CSV
9. Email: user@example.com
10. Submit
```

Response: "Your request has been queued. Token: ABC123XYZ"

### Tracking Request

```
1. Visit /track-data-feed
2. Enter token: ABC123XYZ
3. Click "Track"
```

Status shown:
- Queued
- Processing
- Complete (with download link)
- Failed (with error message)

### Downloading File

When email received:
1. Click download link
2. File downloads as ZIP
3. Extract ZIP to access CSV/XML

Or manually:
```bash
curl http://localhost/data-feeds/download/ABC123XYZ -o export.zip
```

### Form Field Dependencies

Fields update dynamically:
- **Department**: Updates based on agency selection
- **Expense Category**: Updates based on department
- **Budget Code**: Updates based on expense category
- **Budget Name**: Updates based on budget code

### API Integration

Forms use checkbook_api internally:
```php
// In form submission
$criteria = new SearchCriteria($form_values);
$api = new CheckBookAPI($criteria);
$token = $api->queueRequest($email);
```

## Testing

### Manual Testing

#### Scenario 1: Budget Export
- **Steps**: Request budget data with filters
- **Expected**: Token returned, email received, file downloads

#### Scenario 2: Large Dataset
- **Steps**: Request all spending data (no filters)
- **Expected**: Queued, processes in background, email sent

#### Scenario 3: Tracking
- **Steps**: Submit request, track with token
- **Expected**: Status updates from queued to complete

#### Scenario 4: Download
- **Steps**: Click download link from email
- **Expected**: ZIP file downloads with CSV/XML

#### Scenario 5: Form Validation
- **Steps**: Submit form without required fields
- **Expected**: Validation errors displayed

#### Scenario 6: Dynamic Fields
- **Steps**: Select agency, watch department field update
- **Expected**: Department options filtered by agency

#### Scenario 7: NYCHA Data
- **Steps**: Select NYCHA datasource, request spending data
- **Expected**: NYCHA-specific data exported

#### Scenario 8: XML Format
- **Steps**: Request data in XML format
- **Expected**: XML file in ZIP download

#### Scenario 9: Invalid Token
- **Steps**: Track with non-existent token
- **Expected**: Error message displayed

#### Scenario 10: Email Delivery
- **Steps**: Submit request, check email
- **Expected**: Email with download link received

### Testing Checklist
- [ ] Budget form works
- [ ] Spending form works
- [ ] Contracts form works
- [ ] Payroll form works
- [ ] Revenue form works
- [ ] CSV export works
- [ ] XML export works
- [ ] Queue processing works
- [ ] Email delivery works
- [ ] Download links work
- [ ] Tracking page works
- [ ] Token validation works
- [ ] Form validation works
- [ ] Dynamic fields update
- [ ] NYCHA datasource works
- [ ] EDC/OGE datasource works
- [ ] Large datasets handled
- [ ] ZIP files created correctly
- [ ] Files expire properly
- [ ] Error handling works

### Debugging

```bash
# Check queue
drush queue:list
drush queue:run checkbook_datafeeds_queue

# Check logs
drush watchdog:show --type=checkbook_datafeeds

# List export files
ls -lh sites/default/files/checkbook_datafeeds/

# Test form submission
# Use browser DevTools Network tab

# Check email queue
drush queue:list | grep mail
```

## Best Practices

1. **Queue Large Requests**: Always queue requests >1000 records
2. **Validate Input**: Use form validation to prevent bad requests
3. **Monitor Queue**: Regularly check queue processing
4. **Clean Old Files**: Remove expired export files
5. **Email Testing**: Test email delivery in staging
6. **Error Handling**: Provide clear error messages
7. **Performance**: Optimize queries for large exports
8. **Security**: Validate tokens, sanitize inputs
9. **User Feedback**: Show clear status messages
10. **Documentation**: Keep API docs updated

## Related Modules

- **checkbook_api**: Core API for data export
- **checkbook_advanced_search**: Similar filtering interface
- **checkbook_project**: Core utilities
- **checkbook_infrastructure_layer**: Constants and utilities
- **checkbook_log**: Logging functionality

## Support

For issues:
- Review form classes in `src/Form/`
- Check utility classes in `src/Utilities/`
- Review controller in `src/Controller/DefaultController.php`
- Check config files in `config/`
- Test AJAX endpoints
- Review queue processing
- Check email configuration
- Verify file permissions on export directory
