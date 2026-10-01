# Checkbook Landing Page

Domain-specific landing page controllers and routing for Budget, Spending, Contracts, Payroll, and Revenue with multi-datasource support.

## Table of Contents

- [Module Purpose](#module-purpose)
- [Module Functionality](#module-functionality)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Testing](#testing)

## Module Purpose

Provides dedicated landing page controllers for each financial domain. Handles routing, parameter processing, and content rendering for NYC, NYCHA, and EDC/OGE datasources.

Key capabilities:
- Domain-specific landing pages (Budget, Spending, Contracts, Payroll, Revenue)
- Multi-datasource routing (Citywide, NYCHA, EDC/OGE)
- M/WBE landing pages
- Subvendor landing pages
- Pending contracts pages
- Parameter-based filtering
- Block-based widget filters

## Module Functionality

### Core Features

#### Landing Page Controllers
- **Budget Landing**: NYC and NYCHA budget pages
- **Spending Landing**: NYC, NYCHA, M/WBE, Subvendor spending pages
- **Contracts Landing**: NYC, NYCHA, M/WBE, Subvendor, Pending contracts
- **Payroll Landing**: Citywide and NYCHA payroll pages
- **Revenue Landing**: NYC and NYCHA revenue pages

#### Datasource Support
- **Citywide (NYC)**: Primary city financial data
- **NYCHA**: NYC Housing Authority data
- **EDC/OGE**: Economic Development Corporation data

#### Special Landing Pages
- **M/WBE Pages**: Minority/Women-owned Business Enterprise views
- **Subvendor Pages**: Prime vendor and subvendor relationships
- **Pending Contracts**: Expense and revenue pending contracts

### Technical Components

#### Controllers

**BudgetLandingController** (`src/Controller/BudgetLandingController.php`):
- `nycBudget()`: NYC budget landing page
- `nychaBudget()`: NYCHA budget landing page

**SpendingLandingController** (`src/Controller/SpendingLandingController.php`):
- `nycSpending()`: NYC spending landing page
- `nychaSpending()`: NYCHA spending landing page
- `subvendorSpending()`: Subvendor spending pages
- `mwbeSpending()`: M/WBE prime spending
- `mwbesubSpending()`: M/WBE subvendor spending

**ContractLandingController** (`src/Controller/ContractLandingController.php`):
- `nycContracts()`: NYC contracts landing page
- `nychaContracts()`: NYCHA contracts landing page
- `contractsRevenue()`: Revenue contracts
- `nycMwbeContracts()`: M/WBE contracts
- `nycSubvendorContracts()`: Subvendor contracts
- `nycMwbeSubvendorContracts()`: M/WBE subvendor contracts
- `ContractsPendingExpense()`: Pending expense contracts
- `ContractsPendingRevenue()`: Pending revenue contracts

**PayrollLandingController** (`src/Controller/PayrollLandingController.php`):
- `citywideNychaPayroll()`: Citywide and NYCHA payroll

**RevenueLandingController** (`src/Controller/RevenueLandingController.php`):
- `nycRevenue()`: NYC revenue landing page
- `nychaRevenue()`: NYCHA revenue landing page

#### Utilities
- **LandingPageUtilities** (`src/Utilities/`): Helper functions for landing page processing

#### Layout
- **LandingPageLayout** (`src/Layout/`): Layout configuration for landing pages

#### Path Processor
- **PathProcessor** (`src/PathProcessor/`): URL path processing and parameter extraction

#### Block Plugin
- **WidgetControllerFilterBlock** (`src/Plugin/Block/WidgetControllerFilterBlock.php`): Block for widget filters on landing pages

#### Routes
18 routes defined in `checkbook_landing_page.routing.yml`:
- Budget routes (2)
- Revenue routes (2)
- Spending routes (5)
- Contracts routes (8)
- Payroll routes (1)

### URL Structure

Landing pages use parameter-based URLs:
```
/budget/{params}
/spending_landing/{params}
/contracts_landing/{params}
/payroll/{params}
/revenue/{params}
```

Parameters format:
```
/budget/year/2024/agency/057
/spending_landing/dashboard/ms/year/2024
/contracts_landing/mwbe_landing/status/registered
```

## Installation

### Dependencies
- Drupal 10 or 11
- `checkbook_project` (core functionality)
- `checkbook_infrastructure_layer` (utilities)
- Node entity support

### Installation Steps

```bash
# Enable module
drush en checkbook_landing_page

# Clear cache
drush cr

# Verify routes
drush route:list | grep landing
```

## Configuration

### Routes Configuration

All routes defined in `checkbook_landing_page.routing.yml`:

**Budget Routes**:
- `/budget/{params}` - NYC budget
- `/nycha_budget/{params}` - NYCHA budget

**Revenue Routes**:
- `/revenue/{params}` - NYC revenue
- `/nycha_revenue/{params}` - NYCHA revenue

**Spending Routes**:
- `/spending_landing/{params}` - NYC spending
- `/nycha_spending/{params}` - NYCHA spending
- `/spending_landing/dashboard/ss/{params}` - Subvendor spending
- `/spending_landing/dashboard/sp/{params}` - Prime vendor spending
- `/spending_landing/dashboard/mp/{params}` - M/WBE prime spending
- `/spending_landing/dashboard/ms/{params}` - M/WBE subvendor spending

**Contracts Routes**:
- `/contracts_landing/{params}` - NYC contracts
- `/nycha_contracts/{params}` - NYCHA contracts
- `/contracts_revenue_landing/{params}` - Revenue contracts
- `/contracts_landing/mwbe_landing/{params}` - M/WBE contracts
- `/contracts_landing/subvendor_landing/{params}` - Subvendor contracts
- `/contracts_landing/mwbe_subvendor/{params}` - M/WBE subvendor contracts
- `/contracts_pending_exp_landing/{params}` - Pending expense contracts
- `/contracts_pending_rev_landing/{params}` - Pending revenue contracts

**Payroll Routes**:
- `/payroll/{params}` - Citywide/NYCHA payroll

### Parameter Processing

Parameters extracted from URL path:
```php
// Example: /budget/year/2024/agency/057
// Parsed as: ['year' => '2024', 'agency' => '057']
```

### Node Integration

Controllers load and render node content:
```php
$node = Node::load($nodeId);
return ['#markup' => render($node)];
```

## Usage

### Accessing Landing Pages

**Budget Landing**:
```
# NYC budget
/budget

# With filters
/budget/year/2024/agency/057

# NYCHA budget
/nycha_budget/year/2024
```

**Spending Landing**:
```
# NYC spending
/spending_landing

# NYCHA spending
/nycha_spending/year/2024

# M/WBE spending
/spending_landing/dashboard/mp/year/2024

# Subvendor spending
/spending_landing/dashboard/ss/vendor/12345
```

**Contracts Landing**:
```
# NYC contracts
/contracts_landing

# M/WBE contracts
/contracts_landing/mwbe_landing/status/registered

# Pending contracts
/contracts_pending_exp_landing/agency/057
```

**Payroll Landing**:
```
# Payroll
/payroll/year/2024/agency/057
```

**Revenue Landing**:
```
# NYC revenue
/revenue/year/2024

# NYCHA revenue
/nycha_revenue/year/2024
```

### Parameter Examples

**Fiscal Year Filter**:
```
/budget/year/2024
```

**Agency Filter**:
```
/spending_landing/agency/057
```

**Multiple Filters**:
```
/contracts_landing/year/2024/agency/057/status/registered
```

**M/WBE Category**:
```
/spending_landing/dashboard/mp/mwbe/2
```

### Programmatic Access

```php
use Drupal\checkbook_landing_page\Controller\BudgetLandingController;

$controller = new BudgetLandingController();
$build = $controller->nycBudget('year/2024/agency/057');
```

### Block Usage

Widget filter block can be placed on landing pages:
```php
// In block configuration
$block = \Drupal::service('plugin.manager.block')
  ->createInstance('widget_controller_filter_block');
```

## Testing

### Manual Testing

#### Scenario 1: NYC Budget Landing
- **Steps**: Navigate to `/budget`
- **Expected**: Budget landing page displays

#### Scenario 2: NYCHA Budget Landing
- **Steps**: Navigate to `/nycha_budget`
- **Expected**: NYCHA budget page displays

#### Scenario 3: Spending with Filters
- **Steps**: Navigate to `/spending_landing/year/2024/agency/057`
- **Expected**: Filtered spending data displays

#### Scenario 4: M/WBE Spending
- **Steps**: Navigate to `/spending_landing/dashboard/mp`
- **Expected**: M/WBE spending page displays

#### Scenario 5: Subvendor Contracts
- **Steps**: Navigate to `/contracts_landing/subvendor_landing`
- **Expected**: Subvendor contracts page displays

#### Scenario 6: Pending Contracts
- **Steps**: Navigate to `/contracts_pending_exp_landing`
- **Expected**: Pending expense contracts display

#### Scenario 7: Payroll Landing
- **Steps**: Navigate to `/payroll/year/2024`
- **Expected**: Payroll data for 2024 displays

#### Scenario 8: Revenue Landing
- **Steps**: Navigate to `/revenue`
- **Expected**: Revenue landing page displays

#### Scenario 9: Parameter Processing
- **Steps**: Navigate with multiple parameters
- **Expected**: All filters applied correctly

#### Scenario 10: Invalid Route
- **Steps**: Navigate to non-existent landing page
- **Expected**: 404 error

### Testing Checklist
- [ ] All budget routes work
- [ ] All spending routes work
- [ ] All contracts routes work
- [ ] All payroll routes work
- [ ] All revenue routes work
- [ ] Parameters parsed correctly
- [ ] Filters applied correctly
- [ ] NYC datasource works
- [ ] NYCHA datasource works
- [ ] M/WBE pages work
- [ ] Subvendor pages work
- [ ] Pending contract pages work
- [ ] Block placement works
- [ ] Path processing works
- [ ] 404 handling works

### Debugging

```bash
# List all routes
drush route:list | grep landing

# Test specific route
drush route:debug checkbook_landing_page.nyc_budget

# Check controller
drush php-eval "print_r(get_class_methods('Drupal\checkbook_landing_page\Controller\BudgetLandingController'));"

# Test URL
curl http://localhost/budget/year/2024

# Check logs
drush watchdog:show --type=checkbook_landing_page
```

## Best Practices

1. **Consistent Parameters**: Use standard parameter names across domains
2. **Validate Parameters**: Validate all URL parameters
3. **Error Handling**: Handle missing/invalid parameters gracefully
4. **Performance**: Cache landing page content when possible
5. **SEO**: Use descriptive URLs and titles
6. **Accessibility**: Ensure keyboard navigation works
7. **Mobile**: Test on mobile devices
8. **Documentation**: Document parameter formats

## Related Modules

- **checkbook_project**: Core functionality
- **checkbook_infrastructure_layer**: Utilities, constants
- **checkbook_custom_breadcrumbs**: Breadcrumb generation for landing pages
- **widget_config**: Widget configuration for landing pages

## Support

For issues:
- Review controllers in `src/Controller/`
- Check routing in `checkbook_landing_page.routing.yml`
- Review path processor in `src/PathProcessor/`
- Check utilities in `src/Utilities/`
- Test routes with `drush route:list`
- Verify parameter parsing
- Check logs for errors
