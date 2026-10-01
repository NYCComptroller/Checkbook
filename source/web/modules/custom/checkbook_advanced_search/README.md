# Checkbook Advanced Search

Advanced search form system with accordion interface for filtering financial data across Budget, Spending, Contracts, Payroll, and Revenue domains.

## Table of Contents

- [Module Purpose](#module-purpose)
- [Module Functionality](#module-functionality)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Testing](#testing)

## Module Purpose

The Checkbook Advanced Search module provides a comprehensive search interface for the NYC Checkbook financial transparency platform. It displays an advanced search form in a dialog box with an accordion interface to organize different search domains (Budget, Spending, Contracts, Payroll, and Revenue).

The module enables users to:
- Perform complex, multi-criteria searches across financial datasets
- Filter data by agency, vendor, fiscal year, amount ranges, and domain-specific fields
- Access autocomplete functionality for efficient data entry
- Create custom alerts based on search criteria
- Export search results for further analysis

## Module Functionality

The module provides:

### Core Features
- **Accordion-Based Search Interface**: Organized search forms for each financial domain
- **Multi-Domain Support**: Separate search forms for Budget, Spending, Contracts, Payroll, and Revenue
- **Dynamic Autocomplete**: Context-aware autocomplete for agencies, vendors, budget codes, and other fields
- **Advanced Filtering**: Support for ranges, multiple selections, and conditional fields
- **Alert Integration**: Create alerts based on search criteria (when used with checkbook_alerts module)
- **Results Processing**: Domain-specific result handlers that format and display search results

### Search Domains

1. **Budget Search** (`budget_advanced_search.inc`)
   - Fiscal year filtering
   - Agency and department selection
   - Expense category and budget code filtering
   - Adopted/Modified budget amount ranges

2. **Spending Search** (`spending_advanced_search.inc`)
   - Vendor and agency filtering
   - Contract ID and document ID search
   - Amount ranges and fiscal year selection
   - M/WBE and industry type filtering

3. **Contracts Search** (`contracts_advanced_search.inc`)
   - Contract type and status filtering
   - Vendor and agency selection
   - Award method and registration date ranges
   - M/WBE and industry classification

4. **Payroll Search** (`payroll_advanced_search.inc`)
   - Employee name and title search
   - Agency filtering
   - Salary range filtering
   - Fiscal year selection

5. **Revenue Search** (`revenue_advanced_search.inc`)
   - Revenue category and source filtering
   - Agency selection
   - Fiscal year and amount ranges
   - Fund class filtering

### Technical Components

#### Form System
- **CheckbookAdvancedSearchForm** (`src/Form/CheckbookAdvancedSearchForm.php`): Main form builder with 847 lines handling all domain forms
- **Form Classes** (`src/Form/`):
  - `Field.php`: Field definition and validation
  - `FieldDef.php`: Field metadata and configuration
  - `FieldType.php`: Field type enumeration
  - `Column.php`: Column definitions for results
  - `Content.php`: Content rendering utilities
  - `Form.php`: Form building utilities

#### Controller
- **DefaultController** (`src/Controller/DefaultController.php`): Handles form display and autocomplete routes

#### Blocks
- **CheckbookAdvancedSearchForm**: Block for embedding search form
- **CheckbookAdvancedSearchLink**: Block for search dialog trigger link

#### JavaScript Libraries
- `advanced-search.js`: Main search form interactions
- `advanced-search-common.js`: Shared utilities across domains
- `advanced-search-budget.js`: Budget-specific interactions
- `advanced-search-spending.js`: Spending-specific interactions
- `advanced-search-contracts.js`: Contracts-specific interactions
- `advanced-search-payroll.js`: Payroll-specific interactions
- `advanced-search-revenue.js`: Revenue-specific interactions
- `advanced-search-checkbook-alert.js`: Alert creation functionality

## Installation

### Dependencies

- Drupal 10 or 11
- `checkbook_project` module (required)
- `checkbook_infrastructure_layer` module (for constants and utilities)
- `checkbook_datafeeds` module (for form utilities)
- `checkbook_log` module (for logging)
- `data_controller` module (for data operations)
- `jquery_plugins` module (for searchable dropdown)
- `checkbook_alerts` module (optional, for alert functionality)

### Installation Steps

1. Ensure all dependencies are installed and enabled
2. Enable the module:
   ```bash
   drush en checkbook_advanced_search
   ```
3. Clear cache:
   ```bash
   drush cr
   ```
4. Verify the search form is accessible at `/advanced-search`

## Configuration

The module is primarily configured through code rather than a UI. Configuration involves:

### Routes

The module defines routes in `checkbook_advanced_search.routing.yml`:

- **Main Search Form**: `/advanced-search`
- **Autocomplete Endpoints**: Various paths for domain-specific autocomplete
  - Budget expense category: `/advanced-search/autocomplete/budget/expcategory/{params}`
  - Budget code: `/advanced-search/autocomplete/budget/budgetcode/{params}`
  - Budget name: `/advanced-search/autocomplete/budget/budgetname/{params}`
  - Additional routes for other domains

### Form Configuration

Search forms are configured in `src/config/` directory with JSON configuration files for each domain:
- `budget_advanced_search_config.json`
- `spending_advanced_search_config.json`
- `contracts_advanced_search_config.json`
- `payroll_advanced_search_config.json`
- `revenue_advanced_search_config.json`

These files define:
- Field definitions and types
- Validation rules
- Autocomplete configurations
- Result column mappings

### Styling

The module includes CSS in `css/advanced-search.css` with styling for:
- Accordion interface
- Form fields and labels
- Year select dropdowns (width: 90px)
- Prime and sub-vendor notes
- Dialog box appearance

### JavaScript Configuration

JavaScript libraries are defined in `checkbook_advanced_search.libraries.yml`:
- Domain-specific libraries for each search type
- Common utilities library
- Alert integration library
- Dependencies on jQuery and jQuery UI Dialog

## Usage

### Accessing the Search Form

1. **Direct Access**: Navigate to `/advanced-search`
2. **Block Placement**: Add the "Checkbook Advanced Search Form Block" to a region
3. **Link Block**: Use the "New Adv Search Link" block to trigger a dialog

### Performing a Search

1. **Select Domain**: Click on the accordion section for the desired domain (Budget, Spending, etc.)
2. **Enter Criteria**: Fill in search fields (agency, fiscal year, amounts, etc.)
3. **Use Autocomplete**: Start typing in autocomplete fields for suggestions
4. **Submit Search**: Click the search button to view results
5. **Refine Results**: Adjust criteria and re-submit as needed

### Creating Alerts

When integrated with the `checkbook_alerts` module:

1. Perform a search with desired criteria
2. Click "Create Alert" button
3. Configure alert schedule and notification preferences
4. Save alert to receive notifications when matching data changes

### Autocomplete Functionality

The module provides intelligent autocomplete for:
- **Agencies**: Filtered by datasource and context
- **Vendors**: Filtered by agency and fiscal year
- **Budget Codes**: Filtered by agency, department, and expense category
- **Budget Names**: Filtered by related budget codes
- **Expense Categories**: Filtered by agency and fiscal year

Autocomplete is context-aware and filters options based on other selected fields.

### Result Processing

Each domain has a dedicated result processor:
- Formats data for display
- Applies domain-specific business logic
- Generates export-ready datasets
- Handles pagination and sorting

### Integration with Other Modules

The module integrates with:
- **checkbook_project**: Core utilities and data access
- **checkbook_datafeeds**: Export functionality
- **checkbook_alerts**: Alert creation and management
- **checkbook_smart_search**: Complementary search interface
- **widget_framework**: Result display widgets

## Testing

### Manual Testing

#### Scenario 1: Basic Search Functionality
- **Description**: Verify search form loads and accepts input
- **Steps**:
  1. Navigate to `/advanced-search`
  2. Verify accordion sections are visible (Budget, Spending, Contracts, Payroll, Revenue)
  3. Expand each section and verify fields are present
  4. Enter valid search criteria in one domain
  5. Submit the search
- **Expected Result**: Search form displays correctly, accepts input, and returns results

#### Scenario 2: Autocomplete Functionality
- **Description**: Test autocomplete fields for accurate suggestions
- **Steps**:
  1. Open Budget search section
  2. Select a fiscal year
  3. Start typing in the Agency field
  4. Verify autocomplete suggestions appear
  5. Select an agency from suggestions
  6. Test autocomplete in Expense Category field
- **Expected Result**: Autocomplete provides relevant, filtered suggestions based on context

#### Scenario 3: Multi-Criteria Search
- **Description**: Test search with multiple filter criteria
- **Steps**:
  1. Open Spending search section
  2. Select fiscal year: 2024
  3. Enter agency name
  4. Enter amount range: $100,000 - $500,000
  5. Select M/WBE category
  6. Submit search
- **Expected Result**: Results are filtered by all criteria, showing only matching records

#### Scenario 4: Range Filtering
- **Description**: Verify amount range filtering works correctly
- **Steps**:
  1. Open Contracts search section
  2. Enter minimum contract amount: $50,000
  3. Enter maximum contract amount: $200,000
  4. Submit search
  5. Verify all results fall within the specified range
- **Expected Result**: All results have contract amounts between $50,000 and $200,000

#### Scenario 5: Cross-Domain Search
- **Description**: Test switching between different search domains
- **Steps**:
  1. Perform a Budget search with specific criteria
  2. Note the results
  3. Switch to Spending search accordion
  4. Enter different criteria
  5. Submit Spending search
  6. Verify Budget search criteria is not applied to Spending results
- **Expected Result**: Each domain maintains independent search criteria and results

#### Scenario 6: Alert Creation
- **Description**: Test alert creation from search results (requires checkbook_alerts module)
- **Steps**:
  1. Perform a search with specific criteria
  2. Click "Create Alert" button
  3. Configure alert schedule (daily, weekly, monthly)
  4. Enter notification email
  5. Save alert
  6. Verify alert appears in user's alert list
- **Expected Result**: Alert is created successfully with correct criteria and schedule

#### Scenario 7: Form Validation
- **Description**: Test form validation for invalid inputs
- **Steps**:
  1. Open any search section
  2. Enter invalid fiscal year (e.g., "ABCD")
  3. Enter invalid amount range (max < min)
  4. Enter special characters in text fields
  5. Submit form
- **Expected Result**: Validation errors are displayed, form does not submit with invalid data

#### Scenario 8: Responsive Behavior
- **Description**: Test search form on different screen sizes
- **Steps**:
  1. Open search form on desktop browser
  2. Resize window to tablet size (768px)
  3. Resize to mobile size (375px)
  4. Verify accordion expands/collapses correctly
  5. Verify all fields are accessible
  6. Test form submission on mobile
- **Expected Result**: Form is usable and functional at all screen sizes

#### Scenario 9: Dialog Mode
- **Description**: Test search form in dialog/modal mode
- **Steps**:
  1. Click "Advanced Search" link (if using link block)
  2. Verify form opens in dialog overlay
  3. Perform a search
  4. Verify results display correctly
  5. Close dialog
  6. Reopen and verify state is reset
- **Expected Result**: Dialog opens/closes smoothly, search works in dialog mode

#### Scenario 10: Performance with Large Datasets
- **Description**: Test search performance with broad criteria
- **Steps**:
  1. Open Spending search
  2. Select only fiscal year (no other filters)
  3. Submit search
  4. Measure page load time
  5. Verify pagination works
  6. Test sorting on result columns
- **Expected Result**: Search completes within acceptable time (<5 seconds), pagination and sorting work correctly

### Automated Testing

Currently, the module relies on manual testing. Future automated testing could include:

- **PHPUnit Tests**: Unit tests for form validation logic, field definitions, and result processors
- **Functional Tests**: Drupal functional tests for form submission and result display
- **JavaScript Tests**: Jest or similar for testing autocomplete and accordion interactions
- **Integration Tests**: Tests for autocomplete endpoints and data filtering
- **Performance Tests**: Load testing for search with various criteria combinations

### Testing Checklist

- [ ] All five domain search forms load correctly
- [ ] Autocomplete works for all applicable fields
- [ ] Form validation prevents invalid submissions
- [ ] Amount ranges filter results correctly
- [ ] Fiscal year filtering works across all domains
- [ ] Agency filtering returns correct results
- [ ] M/WBE filtering works in Spending and Contracts
- [ ] Results can be exported (if checkbook_datafeeds is enabled)
- [ ] Alerts can be created from search results (if checkbook_alerts is enabled)
- [ ] Dialog mode works correctly
- [ ] Accordion expands/collapses smoothly
- [ ] Form is responsive on mobile devices
- [ ] No JavaScript console errors
- [ ] Search results match expected criteria
- [ ] Pagination works for large result sets

### Known Issues

- Year select dropdowns have fixed width (90px) which may truncate long fiscal year labels
- Autocomplete may be slow with very large datasets
- Dialog mode may have z-index conflicts with other page elements

### Debugging

To debug search issues:

1. **Check Browser Console**: Look for JavaScript errors
2. **Enable Drupal Logging**: Check watchdog logs for PHP errors
3. **Inspect Network Tab**: Verify autocomplete AJAX requests are successful
4. **Review Form State**: Use `dpm($form_state)` to inspect submitted values
5. **Check Database Queries**: Enable query logging to see generated SQL
6. **Verify Permissions**: Ensure user has 'access content' permission

## Best Practices

1. **Always Validate Input**: Use form validation to prevent SQL injection and invalid data
2. **Optimize Autocomplete**: Limit autocomplete results to improve performance
3. **Cache Results**: Consider caching frequently accessed search results
4. **Log Searches**: Use checkbook_log to track search patterns and performance
5. **Test Across Domains**: Ensure changes don't break other search domains
6. **Maintain Consistency**: Keep field naming and behavior consistent across domains
7. **Document Custom Fields**: Add comments for domain-specific field logic
8. **Handle Empty Results**: Provide helpful messages when searches return no results

## Related Modules

- **checkbook_project**: Core utilities and data access layer
- **checkbook_smart_search**: Complementary quick search interface
- **checkbook_alerts**: Alert creation and notification system
- **checkbook_datafeeds**: Export functionality for search results
- **checkbook_infrastructure_layer**: Common constants and utilities
- **widget_framework**: Result display and visualization
- **jquery_plugins**: Searchable dropdown and UI enhancements

## Support

For issues or questions about the advanced search module:
- Review existing search configurations in `src/config/`
- Check JavaScript console for client-side errors
- Review Drupal logs for server-side errors
- Consult the Checkbook NYC development team
- Refer to this README for usage guidelines
