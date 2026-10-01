# Checkbook API Ref Files

Automated reference data file generator for API documentation - creates CSV/Excel code lists weekly via cron.

## Table of Contents

- [Module Purpose](#module-purpose)
- [Module Functionality](#module-functionality)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Testing](#testing)

## Module Purpose

Generates and maintains reference code list files for Checkbook API users. Provides downloadable CSV/Excel files with valid codes for agencies, vendors, departments, budget codes, and other lookup values.

Key capabilities:
- Auto-generate reference files weekly via cron
- CSV and Excel format support
- Public download endpoints (no auth required)
- Email notifications when files ready
- 20+ reference code lists
- SQL-based data extraction

## Module Functionality

### Core Features

#### Reference Code Lists
- **Agency codes**: Agency code and name
- **Vendor codes**: Vendor customer code and legal name
- **Department codes**: Department code, name, agency mapping
- **Budget codes**: Budget code and name
- **Expense categories**: Object class codes
- **Industry types**: Industry type ID and name
- **M/WBE categories**: Minority/Women-owned business codes
- **Revenue classes**: Revenue class codes
- **Fund classes**: Fund class codes
- **Contract types**: Contract type codes
- **Award methods**: Award method codes
- **Document codes**: Document type codes
- **Fiscal years**: Available fiscal years
- **And more**: 20+ total code lists

#### File Generation
- **Automated Cron**: Runs weekly to regenerate files
- **CSV Export**: Comma-separated format
- **Excel Export**: XLSX format with formatting
- **Email Notification**: Alerts when generation complete
- **File Storage**: Stores in public files directory

#### Public Endpoints
- **CSV Download**: `/ref_code_list/{code_list_name}`
- **Excel Download**: `/ref_code_list_excel/{code_list_name}`
- **No Authentication**: Public access for API users

### Technical Components

#### Core Class
- **CheckbookApiRef** (`src/API/CheckbookApiRef.php`): Main class handling generation, storage, email

#### Configuration
- **ref_file_sql.json** (`config/ref_file_sql.json`): SQL queries for each code list
  - 20+ code list definitions
  - Custom SQL per list
  - Force quote fields for proper CSV formatting

#### Controller
- **DefaultController** (`src/Controller/DefaultController.php`): Handles download endpoints

#### Hooks
- **hook_cron()**: Triggers weekly file generation
- **hook_mail()**: Formats email notifications
- **hook_mail_alter()**: Adds priority headers

#### Twig Extension
- **CheckbookApiRefExtension** (`src/Twig/CheckbookApiRefExtension.php`): Template functions

### File Generation Process

1. **Cron Trigger**: Weekly cron runs `checkbook_api_ref_cron()`
2. **SQL Execution**: Queries from `ref_file_sql.json` executed
3. **CSV Creation**: Data formatted as CSV with headers
4. **Excel Creation**: CSV converted to XLSX format
5. **File Storage**: Saved to public files directory
6. **Email Notification**: Sent to configured recipients
7. **Cleanup**: Old files removed

## Installation

### Dependencies
- Drupal 10 or 11
- `checkbook_log` module (for logging)
- Database access to ref tables

### Installation Steps

```bash
# Enable module
drush en checkbook_api_ref

# Clear cache
drush cr

# Test endpoint
curl http://localhost/ref_code_list/agency_code_list
```

### Cron Setup

Module uses Drupal cron:
```bash
# Ensure cron running
drush cron

# Or system crontab
0 2 * * 0 cd /var/www/html && drush cron
```

## Configuration

### Email Recipients

Configure in module settings or code:
```php
// In CheckbookApiRef.php
$recipients = ['api-users@example.com'];
```

### File Storage Location

Files stored in: `public://checkbook_api_ref/`

Default: `/sites/default/files/checkbook_api_ref/`

### Available Code Lists

From `ref_file_sql.json`:
- `agency_code_list`
- `vendor_code_list`
- `department_code_list`
- `spending_mwbe_code_list`
- `industry_code_list`
- `budget_code_list`
- `ref_budget_code_list`
- `budget_expense_category_code_list`
- `revenue_class_code_list`
- `revenue_source_code_list`
- `fund_class_code_list`
- `contract_type_code_list`
- `award_method_code_list`
- `document_code_list`
- `fiscal_year_list`
- `payroll_type_list`
- `employee_title_list`
- And more...

### SQL Configuration

Each code list defined in JSON:
```json
{
  "agency_code_list": {
    "sql": "SELECT DISTINCT agency_code, agency_name FROM ref_agency...",
    "force_quote": ["agency_code"]
  }
}
```

**force_quote**: Fields that need quotes in CSV (prevents Excel auto-formatting)

## Usage

### Download CSV File

```bash
# Agency codes
curl http://localhost/ref_code_list/agency_code_list > agencies.csv

# Vendor codes
curl http://localhost/ref_code_list/vendor_code_list > vendors.csv

# Budget codes
curl http://localhost/ref_code_list/budget_code_list > budget_codes.csv
```

### Download Excel File

```bash
# Agency codes Excel
curl http://localhost/ref_code_list_excel/agency_code_list > agencies.xlsx

# Department codes Excel
curl http://localhost/ref_code_list_excel/department_code_list > departments.xlsx
```

### Manual File Generation

```bash
# Trigger cron manually
drush cron

# Or call directly
drush php-eval "\Drupal::service('checkbook_api_ref.service')->api_ref_cron();"
```

### Using in API Requests

Reference files help construct valid API requests:

```bash
# 1. Download agency codes
curl http://localhost/ref_code_list/agency_code_list > agencies.csv

# 2. Find agency code (e.g., "057" for NYPD)
grep "Police" agencies.csv

# 3. Use in API request
curl -X POST http://localhost/api -d '{
  "global": {"type_of_data": "Spending"},
  "search_criteria": {"agency_code": "057"}
}'
```

### File Format Examples

**CSV Format**:
```csv
agency_code,agency_name
"001","DEPARTMENT OF EDUCATION"
"002","DEPARTMENT OF SOCIAL SERVICES"
"057","POLICE DEPARTMENT"
```

**Excel Format**: Same data with Excel formatting, proper column widths

## Testing

### Manual Testing

#### Scenario 1: CSV Download
- **Steps**: `curl http://localhost/ref_code_list/agency_code_list`
- **Expected**: Valid CSV with headers and data

#### Scenario 2: Excel Download
- **Steps**: Download Excel file, open in Excel
- **Expected**: Properly formatted XLSX file

#### Scenario 3: Cron Generation
- **Steps**: Run `drush cron`, check files directory
- **Expected**: New files created with current timestamp

#### Scenario 4: Email Notification
- **Steps**: Run cron, check email
- **Expected**: Email received with file generation summary

#### Scenario 5: Invalid Code List
- **Steps**: Request non-existent code list
- **Expected**: 404 or error message

#### Scenario 6: Force Quote Fields
- **Steps**: Download agency_code_list, check codes
- **Expected**: Agency codes in quotes (prevents Excel issues)

#### Scenario 7: Multiple Downloads
- **Steps**: Download same file multiple times
- **Expected**: Same content, no errors

#### Scenario 8: Large Code Lists
- **Steps**: Download vendor_code_list (large)
- **Expected**: Complete file, no truncation

#### Scenario 9: SQL Execution
- **Steps**: Check logs during cron
- **Expected**: All SQL queries execute successfully

#### Scenario 10: File Cleanup
- **Steps**: Run cron multiple times, check old files
- **Expected**: Old files removed, only current kept

### Testing Checklist
- [ ] CSV downloads work
- [ ] Excel downloads work
- [ ] Cron generates files
- [ ] Email notifications sent
- [ ] All 20+ code lists available
- [ ] Force quote fields properly quoted
- [ ] Files stored in correct location
- [ ] Old files cleaned up
- [ ] SQL queries execute without errors
- [ ] Large files handled correctly
- [ ] Invalid requests handled gracefully
- [ ] No authentication required
- [ ] Files accessible publicly
- [ ] Excel formatting correct
- [ ] CSV encoding correct (UTF-8)

### Debugging

```bash
# Check cron logs
drush watchdog:show --type=checkbook_api_ref

# List generated files
ls -lh sites/default/files/checkbook_api_ref/

# Test SQL query
drush sql:query "SELECT DISTINCT agency_code, agency_name FROM ref_agency LIMIT 5"

# Manual generation
drush php-eval "checkbook_api_ref_cron();"

# Check email queue
drush queue:list
```

## Best Practices

1. **Regular Cron**: Ensure cron runs weekly
2. **Monitor File Size**: Watch for unexpectedly large files
3. **Test Downloads**: Verify files after generation
4. **Email Monitoring**: Check notification delivery
5. **SQL Optimization**: Keep queries performant
6. **File Cleanup**: Remove old files regularly
7. **Error Logging**: Monitor logs for SQL errors
8. **Public Access**: Ensure files directory publicly accessible

## Related Modules

- **checkbook_api**: Main API module using these reference files
- **checkbook_log**: Logging functionality
- **checkbook_project**: Core utilities

## Support

For issues:
- Check `ref_file_sql.json` for SQL queries
- Review `CheckbookApiRef.php` for generation logic
- Check files directory permissions
- Verify cron running
- Check email configuration
- Review logs for errors
