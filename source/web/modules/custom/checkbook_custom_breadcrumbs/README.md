# Checkbook Custom Breadcrumbs

Dynamic breadcrumb and page title generation for NYC Checkbook navigation with drill-down support and history tracking.

## Table of Contents

- [Module Purpose](#module-purpose)
- [Module Functionality](#module-functionality)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Testing](#testing)

## Module Purpose

Generates context-aware breadcrumbs and page titles for Checkbook pages. Handles complex navigation paths across financial domains with drill-down filtering and maintains breadcrumb history via cookies.

Key capabilities:
- Domain-specific breadcrumb generation (Budget, Spending, Contracts, Payroll, Revenue)
- Dynamic page titles based on context
- Breadcrumb history tracking with cookies
- Multi-datasource support (Citywide, NYCHA, EDC/OGE)
- Drill-down navigation support
- JavaScript integration for client-side updates

## Module Functionality

### Core Features

#### Breadcrumb Generation
- **Budget Breadcrumbs**: Fiscal year, agency, department, expense category paths
- **Spending Breadcrumbs**: Vendor, agency, category, M/WBE drill-downs
- **Contracts Breadcrumbs**: Contract type, status, vendor, agency navigation
- **Payroll Breadcrumbs**: Agency, employee, title, salary range paths
- **Revenue Breadcrumbs**: Revenue source, category, fund class navigation
- **Trends Breadcrumbs**: Financial trends page titles

#### Page Titles
- **Custom Titles**: Context-aware page titles
- **Trend Titles**: Special handling for trends pages
- **Dynamic Updates**: JavaScript-based title updates
- **Datasource Labels**: NYC, NYCHA, EDC/OGE prefixes

#### History Tracking
- **Cookie Storage**: Breadcrumb history in browser cookies
- **Back Navigation**: Support for breadcrumb history
- **Template Integration**: History display in templates

### Technical Components

#### Core Classes

**Breadcrumb Builders**:
- **CheckbookBreadcrumbBuilder** (`src/Breadcrumb/CheckbookBreadcrumbBuilder.php`): Main builder implementing `BreadcrumbBuilderInterface`
- **CheckbookBreadcrumb** (`src/Breadcrumb/CheckbookBreadcrumb.php`): Custom breadcrumb class extending Drupal `Breadcrumb`

**Domain-Specific Classes**:
- **BudgetBreadcrumbs** (`src/BudgetBreadcrumbs.php`): Budget navigation
- **SpendingBreadcrumbs** (`src/SpendingBreadcrumbs.php`): Spending navigation
- **ContractsBreadcrumbs** (`src/ContractsBreadcrumbs.php`): Contracts navigation
- **PayrollBreadcrumbs** (`src/PayrollBreadcrumbs.php`): Payroll navigation
- **RevenueBreacrumbs** (`src/RevenueBreacrumbs.php`): Revenue navigation

**Title Classes**:
- **CustomPageTitle** (`src/CustomPageTitle.php`): General page titles
- **CustomBreadcrumbTitle** (`src/CustomBreadcrumbTitle.php`): Breadcrumb-specific titles
- **TrendPageTitle** (`src/TrendPageTitle.php`): Trends page titles

**Utility**:
- **CustomBreadcrumbs** (`src/CustomBreadcrumbs.php`): Shared breadcrumb utilities

#### Service Registration
- **checkbook_custom_breadcrumbs.breadcrumb**: Breadcrumb builder service with priority 9001 (overrides default)

#### JavaScript Integration
- **breadcrumb-history.js**: Client-side breadcrumb history management
- Cookie-based history storage using `js_cookie` library

#### Templates
- **breadcrumb_history.html.twig**: Breadcrumb history display template

### Breadcrumb Structure

Typical breadcrumb path:
```
Home > Spending > Citywide > Vendors > [Vendor Name] > Fiscal Year 2024
```

With drill-downs:
```
Home > Contracts > NYCHA > Active Contracts > Agency: HPD > Award Method: Competitive
```

## Installation

### Dependencies
- Drupal 10 or 11
- `data_controller` module (required)
- `js_cookie` module (required for history tracking)

### Installation Steps

```bash
# Enable module
drush en checkbook_custom_breadcrumbs

# Clear cache
drush cr
```

## Configuration

### Service Priority

Breadcrumb builder registered with priority **9001** to override Drupal's default breadcrumb builder.

### Cookie Configuration

Breadcrumb history stored in cookies:
- **Cookie name**: `checkbook_breadcrumb_history`
- **Storage**: Client-side via JavaScript
- **Expiration**: Session-based (default)

### Domain Constants

Defined in domain classes:
```php
// ContractsBreadcrumbs.php
const NYC = "New York City";
const NYCHA = "New York City Housing Authority";
const EDC = "Economic Development Corporation";
```

### API Integration

```php
// CustomBreadcrumbs.php
const API_URL = '/data-feeds/api';
const API_TITLE = 'API';
```

## Usage

### Automatic Breadcrumb Generation

Breadcrumbs generated automatically on page load:

```php
// In CheckbookBreadcrumbBuilder
public function build(RouteMatchInterface $route_match) {
  // Analyzes current route
  // Determines domain (Budget, Spending, etc.)
  // Builds appropriate breadcrumb trail
  // Returns CheckbookBreadcrumb object
}
```

### Domain-Specific Examples

#### Budget Page
URL: `/budget/agency/057/fiscal-year/2024`

Breadcrumb:
```
Home > Budget > Citywide > Agency: Police Department > Fiscal Year: 2024
```

#### Spending Page
URL: `/spending/vendor/12345/category/services`

Breadcrumb:
```
Home > Spending > Citywide > Vendor: ABC Corp > Category: Services
```

#### Contracts Page
URL: `/contracts/status/registered/agency/057`

Breadcrumb:
```
Home > Contracts > Citywide > Status: Registered > Agency: Police Department
```

### Custom Page Titles

```php
// Get custom page title
$title = CustomPageTitle::getPageTitle();

// Get breadcrumb title
$breadcrumb_title = CustomBreadcrumbTitle::getCustomBreadcrumbTitle();

// Get trend page title
$trend_title = TrendPageTitle::getTrendPageTitle();
```

### JavaScript Integration

```javascript
// Access breadcrumb title from drupalSettings
const breadcrumbTitle = drupalSettings.checkbook_custom_breadcrumbs.breadcrumbTitle;

// Update breadcrumb dynamically
updateBreadcrumb(newPath, newTitle);
```

### Breadcrumb History

History template usage:
```twig
{# In page template #}
{{ include('@checkbook_custom_breadcrumbs/breadcrumb_history.html.twig') }}
```

### Programmatic Breadcrumb Creation

```php
use Drupal\checkbook_custom_breadcrumbs\Breadcrumb\CheckbookBreadcrumb;

$breadcrumb = new CheckbookBreadcrumb();
$breadcrumb->addLink('Home', '/');
$breadcrumb->addLink('Spending', '/spending');
$breadcrumb->addLink('Vendor Details', '/spending/vendor/12345');
```

## Testing

### Manual Testing

#### Scenario 1: Budget Breadcrumbs
- **Steps**: Navigate to budget page with filters
- **Expected**: Breadcrumb shows Home > Budget > [filters]

#### Scenario 2: Spending Drill-Down
- **Steps**: Click vendor, then agency, then fiscal year
- **Expected**: Breadcrumb updates with each drill-down

#### Scenario 3: NYCHA Datasource
- **Steps**: Switch to NYCHA datasource
- **Expected**: Breadcrumb includes "NYCHA" label

#### Scenario 4: Page Title
- **Steps**: Navigate to contracts page
- **Expected**: Page title reflects current context

#### Scenario 5: Breadcrumb History
- **Steps**: Navigate multiple pages, check history
- **Expected**: History cookie contains visited pages

#### Scenario 6: Back Navigation
- **Steps**: Click breadcrumb link to go back
- **Expected**: Returns to previous page

#### Scenario 7: Multi-Level Drill-Down
- **Steps**: Apply 3+ filters in sequence
- **Expected**: All filters appear in breadcrumb

#### Scenario 8: Trends Pages
- **Steps**: Navigate to trends section
- **Expected**: Trend-specific page titles

#### Scenario 9: API Pages
- **Steps**: Visit API documentation
- **Expected**: Breadcrumb shows API path

#### Scenario 10: JavaScript Updates
- **Steps**: Filter data without page reload
- **Expected**: Breadcrumb updates via JavaScript

### Testing Checklist
- [ ] Budget breadcrumbs generate correctly
- [ ] Spending breadcrumbs generate correctly
- [ ] Contracts breadcrumbs generate correctly
- [ ] Payroll breadcrumbs generate correctly
- [ ] Revenue breadcrumbs generate correctly
- [ ] Page titles reflect context
- [ ] Datasource labels appear (NYC, NYCHA, EDC)
- [ ] Breadcrumb history tracked in cookies
- [ ] Back navigation works
- [ ] Multi-level drill-downs supported
- [ ] JavaScript integration works
- [ ] Trends pages have correct titles
- [ ] API pages have correct breadcrumbs
- [ ] No duplicate breadcrumb items
- [ ] Links in breadcrumbs work

### Debugging

```bash
# Check breadcrumb builder service
drush service:list | grep breadcrumb

# Clear cache if breadcrumbs not updating
drush cr

# Check JavaScript console for errors
# Browser DevTools > Console

# Inspect cookies
# Browser DevTools > Application > Cookies

# Check route matching
drush php-eval "print_r(\Drupal::routeMatch()->getRouteName());"
```

## Best Practices

1. **Cache Invalidation**: Clear cache after breadcrumb logic changes
2. **Cookie Management**: Monitor cookie size for history tracking
3. **Performance**: Minimize database queries in breadcrumb generation
4. **Consistency**: Use domain classes for consistent formatting
5. **Testing**: Test all drill-down combinations
6. **JavaScript**: Ensure JS updates don't conflict with page loads
7. **Accessibility**: Ensure breadcrumbs are keyboard navigable
8. **Mobile**: Test breadcrumb display on small screens

## Related Modules

- **checkbook_project**: Core utilities and routing
- **data_controller**: Data access for breadcrumb context
- **js_cookie**: Cookie management for history
- **checkbook_infrastructure_layer**: Common utilities

## Support

For issues:
- Review domain-specific classes in `src/`
- Check `CheckbookBreadcrumbBuilder.php` for routing logic
- Verify service registration in `checkbook_custom_breadcrumbs.services.yml`
- Test JavaScript in browser console
- Check cookie storage in DevTools
- Review template in `templates/breadcrumb_history.html.twig`
