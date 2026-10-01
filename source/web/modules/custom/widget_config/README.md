# Widget Configurations

Provides JSON configuration files, Twig templates, Twig extensions, and supporting front-end libraries used by the Checkbook NYC widget framework (landing page tables, transaction tables, facets, custom template rendering, and Highcharts configuration).

## Table of Contents

- [Module Purpose](#module-purpose)
- [Module Functionality](#module-functionality)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Testing](#testing)

## Module Purpose

The Widget Config module provides JSON-based configuration files for widgets throughout the Checkbook NYC financial transparency platform. It serves as the configuration layer for the widget framework, defining how widgets are structured, what data they display, and how they are rendered.

This module centralizes widget configurations in JSON format, making it easy to:
- Define widget data sources and queries
- Configure chart and table visualizations (Highcharts)
- Set up parameter mappings and validation
- Define transformation logic for data processing
- Specify template files for rendering
- Maintain consistent styling and theming across widgets

## Module Functionality

The module provides:
- **JSON Configuration System**: Declarative widget definitions with embedded PHP for dynamic behavior
- **Highcharts Integration**: Chart configurations with standardized styling (Roboto fonts, consistent colors)
- **Data Transformation Pipeline**: PHP-based data processing and business logic
- **Template System**: Twig templates organized by datasource and domain
- **Twig Extensions**: Domain-specific functions for Budget, Contracts, Payroll, Revenue, Spending, and Trends
- **Utility Classes**: Reusable PHP utilities for grid views, contract details, and payroll summaries

Additional functionality implemented in code:

- **Twig extension services**: `widget_config.services.yml` registers many Twig extensions (tagged `twig.extension`) under `src/Twig/**`, plus a general-purpose `WidgetConfigExtension`.
- **Preprocess + theme hooks**: `widget_config.module` includes preprocess functions for specific templates and registers at least one explicit theme hook via `hook_theme()` (`retroactivity_ytd_comparison`).
- **Library definitions**: `widget_config.libraries.yml` defines front-end bundles for common widget UI patterns (DataTables/Chosen/scroll plugins, pagination, payroll summaries, and top navigation).
- **Page attachments**: `hook_page_attachments()` conditionally attaches `widget_config/featured-trends` when the current alias is `/featured-trends`.

## Architecture

### Directory Structure

```
widget_config/
├── src/
│   ├── Config/              # JSON configuration files
│   │   ├── Citywide/        # Citywide datasource configurations
│   │   │   ├── Budget/      # Budget widgets
│   │   │   ├── Contracts/   # Contracts widgets
│   │   │   ├── Payroll/     # Payroll widgets
│   │   │   ├── Revenue/     # Revenue widgets
│   │   │   └── Spending/    # Spending widgets
│   │   ├── Edc/             # EDC datasource configurations
│   │   ├── Nycha/           # NYCHA datasource configurations
│   │   └── Sitewide/        # Sitewide configurations
│   ├── Twig/                # Twig extensions
│   │   ├── Budget/          # Budget-specific Twig functions
│   │   ├── Contracts/       # Contracts-specific Twig functions
│   │   ├── Payroll/         # Payroll-specific Twig functions
│   │   ├── Revenue/         # Revenue-specific Twig functions
│   │   ├── Spending/        # Spending-specific Twig functions
│   │   ├── Trends/          # Trends-specific Twig functions
│   │   ├── Sitewide/        # Sitewide Twig functions
│   │   └── Widget/          # General widget Twig functions
│   └── Utilities/           # PHP utility classes
│       ├── ChartGrid.php
│       ├── ContractDetailsUtil.php
│       ├── PayrollAgencySummary.php
│       ├── PayrollGrid.php
│       └── Trends/          # Trends utilities
├── templates/               # Twig template files
│   ├── Citywide/            # Citywide datasource templates
│   ├── Edc/                 # EDC datasource templates
│   ├── Nycha/               # NYCHA datasource templates
│   └── Sitewide/            # Sitewide templates
├── js/                      # JavaScript files
├── images/                  # Image assets
├── widget_config.info.yml   # Module info
├── widget_config.module     # Main module file
├── widget_config.libraries.yml  # Library definitions
└── widget_config.services.yml  # Service definitions
```

## Widget Configuration JSON Structure

Each widget is defined by a JSON configuration file with the following key properties:

### Basic Properties
- **widgetType**: The type of widget (e.g., "highcharts", "ajaxSimple")
- **widgetSubType**: Sub-type specification (e.g., "highcharts", "ajaxSimple")
- **header**: PHP code for generating the widget header
- **dataset**: The dataset name to query (e.g., "checkbook:aggregateon_contract_retroactivity")
- **columns**: Array of columns to retrieve from the dataset
- **orderBy**: Column to order results by
- **defaultParameters**: Default parameter values

### Parameter Handling
- **cleanURLParameters**: Array of URL parameter names to accept
- **urlParamMap**: Mapping of URL parameters to dataset column names
- **adjustParameters**: PHP code to adjust/validate parameters before query execution

### Data Transformation
- **transformationPHP**: PHP code to transform data after retrieval
  - Calculate derived metrics
  - Apply conditional formatting
  - Prepare data for visualization
  - Store additional data for template use

### Visualization Configuration
- **chartConfig**: Highcharts configuration (for chart widgets)
  - chart type, axes, series, tooltips, etc.
- **gridConfig**: Data table configuration (for grid views)
  - template, columns, sorting, etc.
- **summaryView**: Summary view configuration
  - template, title, label settings

### Custom Functions
- **callback**: JavaScript callback functions for chart customization
- **<function>**: Custom formatter functions (defined at end of JSON)

## Example Widget Configuration

```json
{
  "widgetType": "highcharts",
  "widgetSubType": "ajaxSimple",
  "header": "$header = '<h2 class=\"chart-title\">My Chart</h2>'; return $header;",
  "dataset": "checkbook:my_dataset",
  "columns": ["year.year", "amount"],
  "orderBy": "year.year",
  "cleanURLParameters": ["year", "agency"],
  "urlParamMap": {
    "year": "year.year",
    "agency": "agency_id.agency_id"
  },
  "adjustParameters": "PHP code to validate/adjust parameters",
  "transformationPHP": "PHP code to transform data",
  "chartConfig": {
    "chart": {"type": "column"},
    "xAxis": {"categories": []},
    "yAxis": {"title": {"text": "Amount"}},
    "series": []
  }
}
```

## How Widgets Are Loaded

1. **Route**: User visits `/widget/{key}` where `{key}` is the widget identifier
2. **Controller**: `WidgetController::_widget_node_view_page()` handles the request
3. **File Lookup**: `WidgetUtil::getWidgetJsonPath()` locates the JSON config file
4. **Config Load**: JSON file is loaded and parsed
5. **Data Query**: Dataset is queried with adjusted parameters
6. **Transformation**: Data is transformed via `transformationPHP`
7. **Rendering**: Template is rendered with transformed data

## Widget Naming Convention

Widget configuration files follow this naming pattern:
- `{widget-name}.json` - e.g., `contract-volume-chart-1.json`
- Files are organized by datasource, domain, and type
- The filename (without .json) becomes the widget key for routing

## Twig Extensions

The module provides numerous Twig extensions for:
- **Budget**: Budget-specific calculations and formatting
- **Contracts**: Contract-specific utilities and links
- **Payroll**: Payroll calculations and summaries
- **Revenue**: Revenue formatting and display
- **Spending**: Spending calculations and display
- **Trends**: Trend analysis and visualization
- **Sitewide**: Common functions used across the site

## Utilities

### ChartGrid.php
Handles grid view configurations for charts, including:
- Column definitions
- Sorting configuration
- Export functionality
- Data formatting

### ContractDetailsUtil.php
Provides contract-specific utilities:
- Sub-vendor information
- Contract details queries
- Vendor status calculations

### PayrollAgencySummary.php
Payroll agency summary calculations and data processing

### PayrollGrid.php
Payroll-specific grid configurations and data handling

## Template System

Templates are organized by datasource and domain:
- **Citywide**: Templates for Citywide datasource widgets
- **Edc**: Templates for EDC datasource widgets
- **Nycha**: Templates for NYCHA datasource widgets
- **Sitewide**: Templates used across all datasources

Templates receive the following variables:
- `node`: The widget node object containing:
  - `data`: The transformed data
  - `widgetConfig`: The widget configuration
  - `widgetConfig->requestParams`: Current request parameters

### Custom Card / Non-Chart Widget Pattern

Some widgets render as custom HTML cards rather than standard Highcharts charts. This pattern uses:

1. **`template` property at the top level** of the JSON config (not nested under `summaryView`):
   ```json
   {
     "widgetType": "highcharts",
     "widgetSubType": "ajaxSimple",
     "template": "my_custom_card",
     ...
   }
   ```

2. **`hook_theme()` registration** in `widget_config.module` to register the template:
   ```php
   function widget_config_theme($existing, $type, $theme, $path) {
     return [
       'my_custom_card' => [
         'variables' => ['node' => NULL],
         'template' => 'my_custom_card',
         'path' => \Drupal::service('extension.list.module')
           ->getPath('widget_config') . '/templates/Citywide/Contracts',
       ],
     ];
   }
   ```

3. **Computed data on `widgetConfig`** — `transformationPHP` stores derived data directly on `$node->widgetConfig` for template access:
   ```php
   $node->widgetConfig->comparisonData = $comparison_data;
   $node->widgetConfig->yearData = $year_data;
   ```

4. **Minimal `chartConfig`** — a stub chart config is still required for the widget framework to initialize, but the template is the primary render target.

**When to use**: When the visualization is a summary card, KPI indicator, or custom HTML layout rather than a standard chart.

**Example**: `ytd-lateness-rate-comparison.json` — renders a comparison card with year-over-year late registration rate delta, using conditional green/red formatting.

## Integration with Widget Framework

This module works in conjunction with the `widget_framework` module:
- **widget_framework**: Handles widget loading, routing, and rendering
- **widget_config**: Provides the JSON configurations and templates

The widget framework loads JSON files from this module's `src/Config/` directory and uses the templates from the `templates/` directory to render the widgets.

## Dependencies

- Drupal 10 or 11
- data_controller module
- checkbook_project module (for utilities)

## Best Practices

1. **Parameter Validation**: Always use `adjustParameters` to validate and sanitize input
2. **Data Transformation**: Use `transformationPHP` for business logic, not in templates
3. **Template Organization**: Keep templates in the appropriate datasource directory
4. **Naming Conventions**: Use descriptive, consistent naming for widget files
5. **Code Comments**: Document complex PHP code in JSON string values
6. **Error Handling**: Handle edge cases in transformation code (e.g., division by zero)

## Related Modules

- **widget_framework**: Core widget loading and rendering framework
- **widget_highcharts**: Highcharts integration for chart widgets
- **widget_data_tables**: Data table integration for grid widgets
- **widget_services**: Service layer for data operations
- **widget_phpparser**: PHP code parsing for dynamic configurations

## Installation

The Widget Config module is part of the Checkbook NYC custom modules.

### Dependencies

- Drupal 10 or 11
- `data_controller` module
- `checkbook_project` module (for utilities)
- `widget_framework` module (core widget loading and rendering)
- `widget_highcharts` module (Highcharts integration)
- `widget_data_tables` module (data table integration)

### Installation Steps

1. Ensure all dependencies are installed and enabled
2. Enable the module: `drush en widget_config`
3. Clear cache: `drush cr`

## Configuration

The module is configured through JSON files rather than a UI. Configuration files are located in:

```
src/Config/
├── Citywide/     # Citywide datasource configurations
│   ├── Budget/
│   ├── Contracts/
│   ├── Payroll/
│   ├── Revenue/
│   └── Spending/
├── Edc/          # EDC datasource configurations
├── Nycha/        # NYCHA datasource configurations
└── Sitewide/     # Cross-datasource configurations
```

### Widget Configuration Structure

Each widget is defined by a JSON file with these key properties:

#### Basic Properties
- `widgetType`: Widget type (e.g., "highcharts", "ajaxSimple")
- `widgetSubType`: Sub-type specification
- `header`: PHP code for generating the widget header
- `dataset`: Dataset name to query (e.g., "checkbook:aggregateon_contract_retroactivity")
- `columns`: Array of columns to retrieve
- `orderBy`: Column to order results by
- `limit`: Maximum number of records to return

#### Parameter Handling
- `cleanURLParameters`: Array of URL parameter names to accept
- `urlParamMap`: Mapping of URL parameters to dataset column names
- `adjustParameters`: PHP code to validate/adjust parameters before query execution

#### Data Transformation
- `transformationPHP`: PHP code to transform data after retrieval
  - Calculate derived metrics
  - Apply conditional formatting
  - Prepare data for visualization
  - Store additional data on `$node->widgetConfig` for template access

#### Visualization Configuration
- `chartConfig`: Highcharts configuration object
  - `chart`: Chart type, dimensions, margins
  - `xAxis`: X-axis configuration (categories, labels, title)
  - `yAxis`: Y-axis configuration (title, labels, gridlines)
  - `series`: Data series definitions
  - `tooltip`: Tooltip configuration
  - `legend`: Legend configuration
  - `plotOptions`: Series-specific options
- `gridConfig`: Data table configuration (for grid views)
- `summaryView`: Summary view configuration

#### Custom Functions
- `callback`: JavaScript callback functions for chart customization
- `<function>`: Custom formatter functions (defined at end of JSON)

### Example Configuration

```json
{
  "widgetType": "highcharts",
  "widgetSubType": "ajaxSimple",
  "header": "return '<h2 class=\"chart-title\">Contract Volume</h2>';",
  "dataset": "checkbook:aggregateon_contract_retroactivity",
  "columns": ["year.year", "contract_count"],
  "orderBy": "year.year",
  "limit": 10,
  "cleanURLParameters": ["year-end", "agency"],
  "urlParamMap": {
    "year-end": "year.year",
    "agency": "agency_id.agency_id"
  },
  "adjustParameters": "// PHP validation code",
  "transformationPHP": "// PHP transformation code",
  "chartConfig": {
    "chart": {
      "type": "column",
      "height": 400
    },
    "xAxis": {
      "categories": [],
      "title": {
        "text": "Fiscal Year",
        "style": {
          "fontSize": "12px",
          "fontFamily": "Roboto, sans-serif"
        }
      }
    },
    "yAxis": {
      "title": {
        "text": "# of Contracts",
        "style": {
          "fontSize": "12px",
          "fontFamily": "Roboto, sans-serif"
        },
        "margin": 10
      }
    },
    "series": []
  }
}
```

## Usage

### How Widgets Are Loaded

1. **Route**: User visits a widget route (commonly shaped like `/widget/{key}` where `{key}` identifies a widget)
2. **Controller**: The widget framework’s controller (for example `WidgetController::_widget_node_view_page()` in `widget_framework`) handles the request
3. **File Lookup**: `WidgetUtil::getWidgetJsonPath()` locates the JSON config file
4. **Config Load**: JSON file is loaded and parsed
5. **Parameter Processing**: `adjustParameters` validates and transforms URL parameters
6. **Data Query**: Dataset is queried with adjusted parameters
7. **Transformation**: Data is transformed via `transformationPHP`
8. **Rendering**: Template is rendered with transformed data and chart config

### Widget Naming Convention

Widget configuration files follow this naming pattern:
- `{widget-name}.json` or `{id}.json` (many configs in this repository are numeric IDs)
- Files are organized by datasource, domain, and type
- The filename (without .json) becomes the widget key for routing

### Custom Card / Non-Chart Widget Pattern

Some widgets render as custom HTML cards rather than standard Highcharts charts:

1. **`template` property at the top level** of the JSON config:
   ```json
   {
     "widgetType": "highcharts",
     "widgetSubType": "ajaxSimple",
     "template": "my_custom_card",
     ...
   }
   ```

2. **`hook_theme()` registration** in `widget_config.module`:
   ```php
   function widget_config_theme($existing, $type, $theme, $path) {
     return [
       'my_custom_card' => [
         'variables' => ['node' => NULL],
         'template' => 'my_custom_card',
         'path' => \Drupal::service('extension.list.module')
           ->getPath('widget_config') . '/templates/Citywide/Contracts',
       ],
     ];
   }
   ```

3. **Computed data on `widgetConfig`** — `transformationPHP` stores derived data:
   ```php
   $node->widgetConfig->comparisonData = $comparison_data;
   $node->widgetConfig->yearData = $year_data;
   ```

4. **Minimal `chartConfig`** — a stub chart config is still required for initialization

**When to use**: Summary cards, KPI indicators, or custom HTML layouts rather than standard charts.

**Example**: `ytd-lateness-rate-comparison.json` — renders a comparison card with year-over-year late registration rate delta.

### Template System

Templates are organized by datasource and domain:
- **Citywide**: Templates for Citywide datasource widgets
- **Edc**: Templates for EDC datasource widgets
- **Nycha**: Templates for NYCHA datasource widgets
- **Sitewide**: Templates used across all datasources

Templates receive the following variables:
- `node`: The widget node object containing:
  - `data`: The transformed data
  - `widgetConfig`: The widget configuration
  - `widgetConfig->requestParams`: Current request parameters
  - Custom properties set in `transformationPHP`

### Front-end libraries

This module defines multiple Drupal libraries in `widget_config.libraries.yml` that other modules/templates attach as needed:

- **`widget_config/year-list`**
  - Chosen-based “year” dropdown behavior.
- **`widget_config/datatables_ajaxSimple`**
  - DataTables + supporting plugins (Chosen, sticky header, jScrollPane, Buttons/Select/FixedColumns extensions), plus Checkbook-specific JS like `widget_framework/widget_data_tables/js/transactions.js` and export support.
- **`widget_config/datatables_dataTableList`**
  - Similar to `datatables_ajaxSimple`, but also includes narrow-down/faceted-search JS.
- **`widget_config/grid-view-with-input`**
  - DataTables bundle used for grid views that include input fields.
- **`widget_config/simple-pagination`**
  - Ships the `simplePagination` jQuery plugin assets.
- **`widget_config/top-navigation`**
  - Site navigation behaviors (`js/top-navigation.js`).
- **`widget_config/payroll-employee`**, **`widget_config/payroll-month-summary`**, **`widget_config/payroll-agency-summary`**
  - Payroll-specific front-end behaviors.
- **`widget_config/featured-trends`**
  - Attached automatically on `/featured-trends` via `hook_page_attachments()`.

### Twig Extensions

The module provides Twig extensions organized by domain:

- **Budget** (`src/Twig/Budget/`): Budget-specific calculations and formatting
- **Contracts** (`src/Twig/Contracts/`): Contract-specific utilities and links
- **Payroll** (`src/Twig/Payroll/`): Payroll calculations and summaries
- **Revenue** (`src/Twig/Revenue/`): Revenue formatting and display
- **Spending** (`src/Twig/Spending/`): Spending calculations and display
- **Trends** (`src/Twig/Trends/`): Trend analysis and visualization
- **Sitewide** (`src/Twig/Sitewide/`): Common functions used across the site
- **Widget** (`src/Twig/Widget/`): General widget functions

### Utility Classes

#### ChartGrid.php
Handles grid view configurations for charts:
- Column definitions
- Sorting configuration
- Export functionality
- Data formatting

#### ContractDetailsUtil.php
Provides contract-specific utilities:
- Sub-vendor information
- Contract details queries
- Vendor status calculations

#### PayrollAgencySummary.php
Payroll agency summary calculations and data processing

#### PayrollGrid.php
Payroll-specific grid configurations and data handling

## Theming and Styling

### Font Standards

All widgets should use standardized fonts:

- **Primary Font**: `Roboto, sans-serif`
- **Condensed Font**: `RobotoCondensed, sans-serif`
- **System Fallback**: `Arial, sans-serif` (for form elements)

**Never use**:
- `Helvetica Neue` (replaced with Roboto)
- `Verdana` (replaced with Roboto)
- `serif` as fallback (always use `sans-serif`)

### Highcharts Styling Standards

#### Y-Axis Titles
```json
"yAxis": {
  "title": {
    "text": "Your Title",
    "style": {
      "fontSize": "12px",
      "fontFamily": "Roboto, sans-serif"
    },
    "margin": 10
  }
}
```

#### X-Axis Labels
```json
"xAxis": {
  "labels": {
    "style": {
      "fontSize": "10px",
      "fontFamily": "Roboto, sans-serif",
      "color": "#222222"
    }
  }
}
```

#### Data Labels
```json
"dataLabels": {
  "style": {
    "fontSize": "11px",
    "fontFamily": "Roboto, sans-serif",
    "color": "#FFFFFF"
  }
}
```

### Color Standards

- **White**: `#FFFFFF` (uppercase in JSON), `#fff` (lowercase in CSS)
- **Black**: `#000000` or `#222222` (for text)
- **Red**: `rgb(196, 77, 77)` (standardized format)
- **Blue** (titles): `#3C5E7C`
- **Gray** (text): `#666`, `#333`
- **Gray** (borders): `#D1D1D1`, `#CACACA`

### Chart Dimension Guidelines

- **Default Height**: 400px (adjust based on data density)
- **Bar Charts**: Reduce height or limit data when bars become cramped
- **Margins**: Use consistent margins (e.g., `marginBottom: 80-100px` for legends)

### CSS Organization

Widget-specific CSS should be added to:
- Module-level: `widget_config/css/` (if applicable)
- Domain-level: Related module CSS (e.g., `checkbook_widget_retroactivity/css/`)

Follow existing patterns:
- Use specific selectors (e.g., `#checkbook-widget-retroactivity-filter-form`)
- Maintain consistent spacing and indentation
- Document complex selectors with comments

## Testing

### Manual Testing

#### Scenario 1: Widget Rendering
- **Description**: Verify widget loads and displays correctly
- **Steps**:
  1. Navigate to `/widget/{widget-key}`
  2. Verify chart/table renders without errors
  3. Check browser console for JavaScript errors
  4. Verify data displays correctly
- **Expected Result**: Widget renders with correct data and styling

#### Scenario 2: Parameter Filtering
- **Description**: Test URL parameter filtering
- **Steps**:
  1. Navigate to widget with no parameters
  2. Add URL parameters (e.g., `/widget/my-widget/year/2024/agency/057`)
  3. Verify data updates based on parameters
  4. Test invalid parameters (should be handled gracefully)
- **Expected Result**: Widget filters data correctly, invalid parameters are handled

#### Scenario 3: Font and Styling Consistency
- **Description**: Verify standardized fonts and colors
- **Steps**:
  1. Open widget in browser
  2. Inspect chart elements (axes, labels, titles)
  3. Verify fonts are Roboto/RobotoCondensed (not Helvetica Neue or Verdana)
  4. Verify colors match standards
  5. Check for `serif` fallbacks (should be `sans-serif`)
- **Expected Result**: All text uses standardized fonts and colors

#### Scenario 4: Responsive Behavior
- **Description**: Test widget at different viewport sizes
- **Steps**:
  1. Open widget in browser
  2. Resize browser window to mobile, tablet, and desktop sizes
  3. Verify chart/table adjusts appropriately
  4. Check for text overflow or cramped layouts
- **Expected Result**: Widget displays correctly at all sizes

#### Scenario 5: Data Transformation
- **Description**: Verify data transformation logic
- **Steps**:
  1. Review `transformationPHP` code in JSON config
  2. Test with various data scenarios (empty data, edge cases)
  3. Verify calculated fields are correct
  4. Check for division by zero or null handling
- **Expected Result**: Data transforms correctly without errors

#### Scenario 6: Template Rendering
- **Description**: Test custom template rendering
- **Steps**:
  1. For widgets with custom templates, verify template loads
  2. Check that `$node->widgetConfig` properties are accessible
  3. Verify Twig functions work correctly
  4. Test with missing or null data
- **Expected Result**: Template renders correctly with all data

### Automated Testing

Currently, widget testing is primarily manual. Future automated testing could include:
- PHPUnit tests for utility classes
- JavaScript tests for chart interactions
- Visual regression testing for chart rendering

### Theme Audit

Run periodic theme audits to check for:
- Font consistency (no Helvetica Neue, Verdana, or serif fallbacks)
- Color standardization
- Proper use of Roboto font family

Use browser DevTools to inspect computed styles and verify standards compliance.

## Best Practices

1. **Parameter Validation**: Always use `adjustParameters` to validate and sanitize input
2. **Data Transformation**: Use `transformationPHP` for business logic, not in templates
3. **Template Organization**: Keep templates in the appropriate datasource directory
4. **Naming Conventions**: Use descriptive, consistent naming for widget files
5. **Code Comments**: Document complex PHP code in JSON string values
6. **Error Handling**: Handle edge cases in transformation code (e.g., division by zero, null data)
7. **Font Standards**: Always use `Roboto, sans-serif` or `RobotoCondensed, sans-serif`
8. **Color Standards**: Use standardized color values (uppercase hex in JSON, lowercase in CSS)
9. **Chart Dimensions**: Adjust height/width based on data density to prevent cramping
10. **Margin Consistency**: Use `margin: 10` for y-axis titles for consistent spacing

## Related Modules

- **widget_framework**: Core widget loading and rendering framework
- **widget_highcharts**: Highcharts integration for chart widgets
- **widget_data_tables**: Data table integration for grid widgets
- **widget_services**: Service layer for data operations
- **widget_phpparser**: PHP code parsing for dynamic configurations
- **checkbook_widget_retroactivity**: Retroactivity-specific widgets and forms

## Support

For issues or questions about widget configurations, refer to:
- Existing widget configurations in `src/Config/` for examples
- The widget framework documentation
- The Checkbook NYC development team
- This README for theming and styling standards
