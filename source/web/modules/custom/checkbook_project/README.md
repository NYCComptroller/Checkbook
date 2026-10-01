# Checkbook Project

Core business logic module providing domain-specific utilities, data processing, and common functionality for all Checkbook financial domains.

## Table of Contents

- [Module Purpose](#module-purpose)
- [Module Functionality](#module-functionality)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Testing](#testing)

## Module Purpose

Central module containing business logic, utility classes, and common functionality for Budget, Spending, Contracts, Payroll, and Revenue domains. Provides data processing, URL generation, widget utilities, and domain-specific calculations.

Key capabilities:
- Domain-specific utility classes (Budget, Spending, Contracts, Payroll, Revenue)
- M/WBE business logic and calculations
- NYCHA-specific utilities
- EDC/OGE utilities
- Widget processing and rendering
- URL generation and parameter handling
- Date utilities and formatting
- Request processing
- Chart generation
- Node summary utilities

## Module Functionality

### Core Features

#### Domain Utilities

**Budget Utilities**:
- Budget calculations and aggregations
- NYCHA budget processing
- Budget code handling
- Fiscal year processing

**Spending Utilities**:
- Spending calculations
- Vendor spending analysis
- M/WBE spending processing
- NYCHA spending utilities
- Spending URL generation

**Contracts Utilities**:
- Master agreement processing
- Child agreement handling
- Pending contract details
- Contract URL generation
- Agreement amount calculations

**Payroll Utilities**:
- Payroll type handling (Salaried/Non-Salaried)
- Payroll calculations
- Employee data processing

**Revenue Utilities**:
- Revenue calculations
- NYCHA revenue processing
- Revenue categorization

#### M/WBE Utilities
- Vendor type classification (Prime/Sub)
- M/WBE category mapping
- M/WBE spending calculations
- Minority type handling

#### Widget Utilities
- Widget processing
- Chart generation
- Grid view links
- Node summary utilities
- Widget configuration handling

#### Common Utilities
- Request parameter processing
- Custom URL generation
- Date utilities
- Checkbook date formatting

#### NYCHA Utilities
- NYCHA contract processing
- NYCHA spending utilities
- NYCHA budget utilities
- NYCHA revenue utilities
- Associated releases

#### EDC/OGE Utilities
- EDC contract processing
- OGE data handling

### Technical Components

#### Directory Structure

```
checkbook_project/
├── src/
│   ├── BudgetUtilities/
│   │   ├── BudgetUtil.php
│   │   └── NychaBudgetUtil.php
│   ├── SpendingUtilities/
│   │   ├── SpendingUtil.php
│   │   ├── NychaSpendingUtil.php
│   │   ├── MwbeSpendingUtil.php
│   │   ├── VendorSpendingUtil.php
│   │   └── SpendingUrlHelper.php
│   ├── ContractsUtilities/
│   │   ├── ContractUtil.php
│   │   ├── MasterAgreement.php
│   │   ├── ChildAgreement.php
│   │   ├── MasterAgreementDetails.php
│   │   ├── ChildAgreementDetails.php
│   │   ├── pendingContractDetails.php
│   │   └── ContractURLHelper.php
│   ├── PayrollUtilities/
│   │   ├── PayrollType.php
│   │   └── PayrollUtil.php
│   ├── RevenueUtilities/
│   │   ├── RevenueUtil.php
│   │   └── NychaRevenueUtil.php
│   ├── MwbeUtilities/
│   │   ├── VendorType.php
│   │   └── MappingUtil.php
│   ├── NychaContractUtilities/
│   │   ├── NYCHAContractUtil.php
│   │   ├── NychaContractDetails.php
│   │   └── NychaContractAssocReleases.php
│   ├── EdcUtilities/
│   │   └── EdcUtilities.php
│   ├── WidgetUtilities/
│   │   ├── WidgetUtil.php
│   │   ├── WidgetProcessor.php
│   │   ├── ChartUtil.php
│   │   └── NodeSummaryUtil.php
│   ├── CommonUtilities/
│   │   ├── RequestUtil.php
│   │   ├── CustomURLHelper.php
│   │   └── CheckbookDateUtil.php
│   ├── EventSubscriber/
│   │   └── InitSubscriber.php
│   └── PathProcessor/
│       └── OutboundPathProcessor.php
├── js/
│   ├── agencies-vendors.js
│   ├── year_select_redirection.js
│   ├── contracts.js
│   ├── transactions.js
│   └── cookie_breadcrumbs.js
└── includes/
```

#### Key Classes

**RequestUtil**:
- Request parameter processing
- Vendor handling
- Dataset queries
- URL parameter extraction

**CustomURLHelper**:
- URL generation
- Parameter encoding
- Path construction

**CheckbookDateUtil**:
- Date formatting
- Fiscal year handling
- Date range processing

**WidgetUtil**:
- Widget rendering
- Data formatting
- Link generation

**WidgetProcessor**:
- Widget data processing
- Aggregation
- Filtering

**ChartUtil**:
- Chart generation
- Grid view links
- Data visualization

**NodeSummaryUtil**:
- Node summary generation
- Data aggregation

**SpendingUtil**:
- Spending calculations
- Amount aggregations
- Vendor analysis

**ContractUtil**:
- Contract processing
- Agreement handling
- Amount calculations

**MappingUtil**:
- M/WBE category mapping
- Minority type mapping
- Vendor type classification

**VendorType**:
- Prime vendor constant: `P`
- Sub vendor constant: `S`

**PayrollType**:
- Salaried constant
- Non-Salaried constant

#### Event Subscriber
- **InitSubscriber**: Handles initialization events

#### Path Processor
- **OutboundPathProcessor**: Processes outbound URLs

#### JavaScript Files
- **agencies-vendors.js**: Agency and vendor interactions
- **year_select_redirection.js**: Fiscal year selection
- **contracts.js**: Contract page functionality
- **transactions.js**: Transaction page functionality
- **cookie_breadcrumbs.js**: Breadcrumb cookie handling

## Installation

### Dependencies
- Drupal 10 or 11
- `checkbook_infrastructure_layer` (constants, utilities)
- `checkbook_log` (logging)
- `data_controller` (data access)

### Installation Steps

```bash
# Enable module
drush en checkbook_project

# Clear cache
drush cr

# Verify utilities available
drush php-eval "use Drupal\checkbook_project\CommonUtilities\RequestUtil; echo 'Loaded';"
```

## Configuration

### Constants

**Vendor Custom Code**:
```php
RequestUtil::VENDOR_CUSTOM_CODE = "0000776804";
```

**Vendor Dataset**:
```php
RequestUtil::VENDOR_DATASET = "checkbook:vendor";
```

**Vendor Types**:
```php
VendorType::$PRIME_VENDOR = 'P';
VendorType::$SUB_VENDOR = 'S';
```

**Payroll Types**:
```php
PayrollType::$SALARIED = 'Salaried';
PayrollType::$NON_SALARIED = 'Non-Salaried';
```

### JavaScript Configuration

JavaScript files loaded globally via info.yml:
- `js/agencies-vendors.js`
- `js/year_select_redirection.js`
- `js/contracts.js`
- `js/transactions.js`
- `js/cookie_breadcrumbs.js`

## Usage

### Using Budget Utilities

```php
use Drupal\checkbook_project\BudgetUtilities\BudgetUtil;

// Process budget code
$budgetCode = BudgetUtil::processBudgetCode($code);

// NYCHA budget
use Drupal\checkbook_project\BudgetUtilities\NychaBudgetUtil;
$title = NychaBudgetUtil::EXPENSE_BUDGET_TRANSACTION;
```

### Using Spending Utilities

```php
use Drupal\checkbook_project\SpendingUtilities\SpendingUtil;

// Calculate spending
$total = SpendingUtil::calculateTotal($data);

// Vendor spending
use Drupal\checkbook_project\SpendingUtilities\VendorSpendingUtil;
$url = VendorSpendingUtil::getSubVendorUrl($row);

// M/WBE spending
use Drupal\checkbook_project\SpendingUtilities\MwbeSpendingUtil;
$mwbeData = MwbeSpendingUtil::processMwbeData($node);
```

### Using Contract Utilities

```php
use Drupal\checkbook_project\ContractsUtilities\MasterAgreement;
use Drupal\checkbook_project\ContractsUtilities\ChildAgreement;

// Master agreement
$master = new MasterAgreement($agreementId);
$master->initializeAmounts();

// Child agreement
$child = new ChildAgreement($agreementId);
$child->initializeAmounts();

// Pending contracts
use Drupal\checkbook_project\ContractsUtilities\pendingContractDetails;
$details = new pendingContractDetails();
$details->getData($node);
```

### Using Widget Utilities

```php
use Drupal\checkbook_project\WidgetUtilities\WidgetUtil;
use Drupal\checkbook_project\WidgetUtilities\ChartUtil;

// Generate grid view link
$link = ChartUtil::generateGridViewLink($node);

// Process widget
use Drupal\checkbook_project\WidgetUtilities\WidgetProcessor;
$processor = new WidgetProcessor();
$result = $processor->processWidget($node);

// Node summary
use Drupal\checkbook_project\WidgetUtilities\NodeSummaryUtil;
$summary = NodeSummaryUtil::getNodeSummary($nid);
```

### Using Common Utilities

```php
use Drupal\checkbook_project\CommonUtilities\RequestUtil;
use Drupal\checkbook_project\CommonUtilities\CustomURLHelper;
use Drupal\checkbook_project\CommonUtilities\CheckbookDateUtil;

// Get request parameter
$year = RequestUtil::getRequestParam('year');

// Generate URL
$url = CustomURLHelper::get_url_param($pathParams, 'agency');

// Format date
$formatted = CheckbookDateUtil::formatDate($date);
```

### Using M/WBE Utilities

```php
use Drupal\checkbook_project\MwbeUtilities\VendorType;
use Drupal\checkbook_project\MwbeUtilities\MappingUtil;

// Check vendor type
if ($vendorType == VendorType::$PRIME_VENDOR) {
  // Prime vendor logic
}

// Map M/WBE category
$category = MappingUtil::mapMwbeCategory($minorityTypeId);
```

### Using NYCHA Utilities

```php
use Drupal\checkbook_project\NychaContractUtilities\NYCHAContractUtil;
use Drupal\checkbook_project\NychaContractUtilities\NychaContractDetails;

// NYCHA contract
$util = new NYCHAContractUtil();
$data = $util->getContractData($contractId);

// Contract details
$details = new NychaContractDetails();
$details->getData($node);
```

### Using Payroll Utilities

```php
use Drupal\checkbook_project\PayrollUtilities\PayrollType;

// Check payroll type
if ($type == PayrollType::$SALARIED) {
  // Salaried employee logic
}
```

## Testing

### Manual Testing

#### Scenario 1: Budget Utilities
- **Steps**: Use BudgetUtil methods
- **Expected**: Budget calculations correct

#### Scenario 2: Spending Utilities
- **Steps**: Calculate spending totals
- **Expected**: Accurate totals

#### Scenario 3: Contract Processing
- **Steps**: Process master/child agreements
- **Expected**: Amounts calculated correctly

#### Scenario 4: M/WBE Classification
- **Steps**: Classify vendor types
- **Expected**: Correct prime/sub classification

#### Scenario 5: Widget Processing
- **Steps**: Process widget data
- **Expected**: Widget renders correctly

#### Scenario 6: URL Generation
- **Steps**: Generate custom URLs
- **Expected**: Valid URLs with parameters

#### Scenario 7: Date Formatting
- **Steps**: Format dates
- **Expected**: Correct format

#### Scenario 8: NYCHA Processing
- **Steps**: Process NYCHA data
- **Expected**: NYCHA-specific logic works

#### Scenario 9: Request Parameters
- **Steps**: Extract request parameters
- **Expected**: Parameters extracted correctly

#### Scenario 10: Chart Generation
- **Steps**: Generate chart links
- **Expected**: Valid chart URLs

### Testing Checklist
- [ ] Budget utilities work
- [ ] Spending utilities work
- [ ] Contract utilities work
- [ ] Payroll utilities work
- [ ] Revenue utilities work
- [ ] M/WBE utilities work
- [ ] Widget utilities work
- [ ] Common utilities work
- [ ] NYCHA utilities work
- [ ] EDC utilities work
- [ ] URL generation works
- [ ] Date formatting works
- [ ] Request processing works
- [ ] JavaScript files load
- [ ] Event subscriber works

### Debugging

```bash
# Test utility class
drush php-eval "use Drupal\checkbook_project\CommonUtilities\RequestUtil; print_r(RequestUtil::VENDOR_CUSTOM_CODE);"

# Check class loading
drush php-eval "class_exists('Drupal\checkbook_project\SpendingUtilities\SpendingUtil') ? print 'Loaded' : print 'Not loaded';"

# Test method
drush php-eval "use Drupal\checkbook_project\MwbeUtilities\VendorType; echo VendorType::\$PRIME_VENDOR;"

# Check JavaScript
# Browser DevTools > Sources > checkbook_project/js/

# Check logs
drush watchdog:show --type=checkbook_project
```

## Best Practices

1. **Use Utilities**: Use provided utilities instead of duplicating logic
2. **Consistent Patterns**: Follow established patterns
3. **Error Handling**: Handle errors gracefully
4. **Logging**: Use LogHelper for logging
5. **Documentation**: Document complex business logic
6. **Testing**: Test utility methods thoroughly
7. **Performance**: Optimize calculations
8. **Reusability**: Write reusable utility methods

## Related Modules

- **checkbook_infrastructure_layer**: Constants, base utilities
- **checkbook_log**: Logging functionality
- **data_controller**: Data access
- **widget_config**: Widget configuration
- **checkbook_metamodel**: Dataset metadata
- All domain modules depend on this module

## Support

For issues:
- Review utility classes in `src/` subdirectories
- Check JavaScript files in `js/`
- Review event subscriber
- Check path processor
- Test utility methods
- Verify constants
- Check logs for errors
- Review business logic documentation
