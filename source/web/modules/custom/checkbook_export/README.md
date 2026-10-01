# Checkbook Export

Quick CSV export functionality for transaction data, grid views, and trends with dialog-based download interface.

## Table of Contents

- [Module Purpose](#module-purpose)
- [Module Functionality](#module-functionality)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Testing](#testing)

## Module Purpose

Provides immediate CSV export functionality for viewing transaction data. Enables users to download current page data, grid results, and trends without queue processing.

Key capabilities:
- One-click CSV export from any page
- Grid view exports
- Trends data exports
- Dialog-based export form
- Immediate download (no queue)
- Current view/filter preservation
- Multi-domain support (Budget, Spending, Contracts, Payroll, Revenue)

## Module Functionality

### Core Features

#### Export Types
- **Transaction Export**: Export current page transactions
- **Grid Export**: Export data grid results
- **Trends Export**: Export financial trends data
- **Filtered Export**: Exports respect current filters

#### Export Interface
- **Dialog Form**: Modal dialog for export options
- **Record Count**: Shows number of records to export
- **Format Selection**: CSV format
- **Immediate Download**: No queue, instant CSV
- **Current Context**: Preserves page filters and parameters

#### Data Handling
- **CSV Format**: Comma-separated values with headers
- **Column Selection**: Exports visible columns
- **Data Formatting**: Proper formatting for amounts, dates
- **Large Datasets**: Handles reasonable dataset sizes
- **Memory Management**: Optimized for performance

### Technical Components

#### Controller
- **DefaultController** (`src/Controller/DefaultController.php`):
  - `checkbook_export_form()`: Display export form
  - `_checkbook_export_transactions()`: Export transactions
  - `_checkbook_export_grid_transactions()`: Export grid data
  - `_checkbook_export_trends()`: Export trends data

#### Form
- **CheckbookExportForm** (`src/Form/CheckbookExportForm.php`): Export dialog form

#### Routes
- **Export Form**: `/export/transactions/form`
- **Export Transactions**: `/export/transactions`
- **Export Grid**: `/export/grid/transactions/{nodeId}`
- **Export Trends**: `/export/trends/download/{nodeId}`

#### JavaScript
- **export.transactions.js**: Dialog handling, AJAX submissions

#### Template
- **checkbook-export-form.html.twig**: Export form template

#### Includes
- **checkbook_export.inc**: Utility functions for export processing

### Export Process

1. **User Clicks Export**: Button on page
2. **Dialog Opens**: Export form displays
3. **Shows Record Count**: Number of records to export
4. **User Confirms**: Clicks download button
5. **CSV Generated**: Server creates CSV file
6. **Download Starts**: Browser downloads file
7. **Dialog Closes**: Export complete

## Installation

### Dependencies
- Drupal 10 or 11
- `checkbook_infrastructure_layer` (utilities)
- `checkbook_log` (logging)
- `data_controller_log` (data logging)
- jQuery UI Dialog

### Installation Steps

```bash
# Enable module
drush en checkbook_export

# Clear cache
drush cr

# Verify export buttons appear on pages
```

## Configuration

### Routes Configuration

Defined in `checkbook_export.routing.yml`:
- Form display route
- Transaction export route
- Grid export route with node ID parameter
- Trends export route with node ID parameter

### JavaScript Libraries

Defined in `checkbook_export.libraries.yml`:
- `export.transactions`: Main export functionality
- Dependencies: jQuery UI Dialog, jQuery UI Position

### Template Variables

Export form template receives:
- `displayRecords`: Number of records to export
- `nodeId`: Current node/page identifier
- `exportType`: Type of export (transactions, grid, trends)

### Memory Limits

For large exports, may need to increase:
```php
ini_set('memory_limit', '512M');
ini_set('max_execution_time', 300);
```

## Usage

### Exporting Transactions

1. **Navigate**: Go to any transaction page (e.g., spending, contracts)
2. **Apply Filters**: Set desired filters (agency, fiscal year, etc.)
3. **Click Export**: Click "Export" button
4. **Dialog Opens**: Export form displays
5. **Review Count**: Check number of records
6. **Download**: Click "Download CSV"
7. **File Downloads**: CSV file downloads to browser

### Exporting Grid Data

1. **View Grid**: Navigate to grid view page
2. **Apply Filters**: Set grid filters
3. **Click Export**: Click grid export button
4. **Download**: CSV downloads immediately

### Exporting Trends

1. **View Trends**: Navigate to trends page
2. **Select Trend**: Choose specific trend chart
3. **Click Export**: Click export button
4. **Download**: Trends data downloads as CSV

### Export Dialog

Dialog shows:
```
Export Transactions

Records to Export: 1,234

[Download CSV] [Cancel]
```

### CSV Output Format

**Example Spending Export**:
```csv
Fiscal Year,Agency,Vendor,Amount,Contract ID
2024,Police Department,ABC Corp,$50000.00,CT123456
2024,Fire Department,XYZ Inc,$75000.00,CT789012
```

**Example Trends Export**:
```csv
Fiscal Year,Revenue,Expenditure,Surplus/Deficit
2020,$85000000000,$90000000000,-$5000000000
2021,$88000000000,$92000000000,-$4000000000
```

### Programmatic Export

```php
// Trigger export programmatically
$controller = new DefaultController();
$response = $controller->_checkbook_export_transactions();
```

### AJAX Export

JavaScript handles export via AJAX:
```javascript
// Open export dialog
jQuery('#export-button').click(function() {
  // Open dialog with export form
  // Submit via AJAX
  // Download CSV
});
```

## Testing

### Manual Testing

#### Scenario 1: Transaction Export
- **Steps**: View spending page, click export
- **Expected**: CSV downloads with spending data

#### Scenario 2: Filtered Export
- **Steps**: Apply filters, export
- **Expected**: CSV contains only filtered data

#### Scenario 3: Grid Export
- **Steps**: View grid, click export
- **Expected**: Grid data exports to CSV

#### Scenario 4: Trends Export
- **Steps**: View trends, export specific trend
- **Expected**: Trend data downloads

#### Scenario 5: Large Dataset
- **Steps**: Export page with 10,000+ records
- **Expected**: Export completes without timeout

#### Scenario 6: Dialog Interaction
- **Steps**: Open dialog, cancel, reopen
- **Expected**: Dialog works correctly

#### Scenario 7: Record Count
- **Steps**: Check record count in dialog
- **Expected**: Accurate count displayed

#### Scenario 8: CSV Format
- **Steps**: Open exported CSV in Excel
- **Expected**: Proper formatting, headers present

#### Scenario 9: Multiple Exports
- **Steps**: Export multiple times in succession
- **Expected**: All exports work correctly

#### Scenario 10: Error Handling
- **Steps**: Export with invalid parameters
- **Expected**: Error message displayed

### Testing Checklist
- [ ] Export button appears on pages
- [ ] Dialog opens correctly
- [ ] Record count accurate
- [ ] CSV downloads
- [ ] Filters preserved in export
- [ ] Grid export works
- [ ] Trends export works
- [ ] CSV format correct
- [ ] Headers included
- [ ] Data formatted properly
- [ ] Large datasets handled
- [ ] Dialog closes after export
- [ ] Multiple exports work
- [ ] Error handling works
- [ ] No memory errors

### Debugging

```bash
# Check logs
drush watchdog:show --type=checkbook_export

# Test export URL directly
curl http://localhost/export/transactions > test.csv

# Check JavaScript console
# Browser DevTools > Console

# Verify route
drush route:list | grep export

# Check memory limit
drush php-eval "echo ini_get('memory_limit');"
```

## Best Practices

1. **Limit Records**: Don't export millions of records at once
2. **Use Filters**: Apply filters before exporting
3. **Monitor Performance**: Watch memory usage
4. **Test Large Exports**: Test with realistic data volumes
5. **Error Handling**: Provide clear error messages
6. **CSV Formatting**: Ensure proper escaping
7. **User Feedback**: Show progress for large exports
8. **File Naming**: Use descriptive file names with timestamps

## Related Modules

- **checkbook_datafeeds**: Queue-based exports for large datasets
- **checkbook_api**: API-based data export
- **checkbook_project**: Core utilities
- **checkbook_infrastructure_layer**: Common utilities
- **checkbook_log**: Logging

## Support

For issues:
- Review controller in `src/Controller/DefaultController.php`
- Check form in `src/Form/CheckbookExportForm.php`
- Review JavaScript in `js/export.transactions.js`
- Check template in `templates/checkbook-export-form.html.twig`
- Review includes in `includes/checkbook_export.inc`
- Test routes in browser
- Check logs for errors
- Verify memory limits
