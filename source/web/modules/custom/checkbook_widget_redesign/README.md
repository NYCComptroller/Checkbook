# Checkbook Widget Redesign

A collection of custom Drupal modules that implement the “Widget Redesign” architecture for Checkbook NYC. The code is organized into three layers:

- **Domain layer** (`checkbook_domain`) – repositories, entity factories, SQL dataset/count factories.
- **Service layer** (`checkbook_services`) – domain-specific services that fetch/shape data and generate URLs for widgets.
- **Infrastructure + presentation layer** (`checkbook_infrastructure_layer`, `widget_controller`, `landing_page_widget_view`, `checkbook_view_configs`) – request utilities, path processing, controllers, Twig helpers, JS/CSS libraries, and view configuration.

This folder is not a single Drupal module; it is a *module suite* under `modules/custom/checkbook_widget_redesign/`.

## Table of Contents

- [Module Purpose](#module-purpose)
- [Module Functionality](#module-functionality)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Testing](#testing)

## Module Purpose

The Checkbook Widget Redesign codebase is intended to modernize and standardize how “widgets” are configured, requested, and rendered throughout the site.

Instead of embedding widget behavior directly in one-off controllers or hard-coded blocks, this suite:

- Centralizes **data access** into repositories and SQL model factories.
- Implements reusable **business services** for each domain (budget, contracts, etc.).
- Provides **presentation controllers** and **AJAX endpoints** that return widget content/data.
- Adds **path processors** to keep legacy URL formats working by normalizing request paths.

## Module Functionality

### Included modules (high level)

#### Business layer

- **`checkbook_domain`**
  - Purpose: domain objects + SQL repositories/factories.
  - Key classes:
    - `Drupal\checkbook_domain\Widget\WidgetRepository`
    - `Drupal\checkbook_domain\Sql\SqlEntityRepository` (used via services)
    - `Drupal\checkbook_domain\Sql\SqlDatasetFactory`, `SqlRecordCountFactory`

- **`checkbook_services`**
  - Purpose: domain-specific services that fetch data, compute derived columns, and build URLs.
  - Example widget service:
    - `Drupal\checkbook_services\Budget\BudgetWidgetService`
  - Common base:
    - `Drupal\checkbook_services\Common\DataService`
      - Adds memcache-backed caching (`_checkbook_dmemcache_get/set`) per datasource + parameter set.

#### Infrastructure layer

- **`checkbook_infrastructure_layer`**
  - Provides shared utilities (e.g., `RequestUtilities`, formatting utilities, datasource constants) used throughout the widget suite.
  - Registers an (currently empty) Kernel request subscriber.

#### Presentation layer

- **`widget_controller`**
  - Route:
    - `/widget_controller/{key}`
  - Controller:
    - `Drupal\widget_controller\Controller\DefaultController::_widget_controller_node_view_page()`
      - Converts `{key}` into a node id using `RequestUtil::_getnodeid()`.
      - Loads widget configuration/content and returns widget body markup.
  - Inbound path processor:
    - `Drupal\widget_controller\PathProcessor\WidgetControllerPathProcessor`
      - Normalizes multiple widget-related URL patterns by replacing slashes with a separator via `RequestUtilities::replaceSlash()`.

- **`landing_page_widget_view`**
  - Route:
    - `/checkbook_views/data_tables/ajax_data/node/{key}`
  - Controller:
    - `Drupal\landing_page_widget_view\Controller\DefaultController::_landing_page_widget_view_ajaxdata()`
      - Loads widget config by node id and returns JSON for the landing-page data table widgets.
  - Twig extension:
    - `Drupal\landing_page_widget_view\Twig\LandingPageExtension`
      - Exposes `landing_page_widget_view_add_js_twig()`.
  - Template:
    - `templates/table_by_rows.html.twig`
      - Renders a table and attaches the DataTables-based library.
  - Library:
    - `landing_page_widget_view/landing_page_data_table`
      - Loads DataTables and `js/landing_page_widget.js`.

- **`checkbook_view_configs`**
  - Purpose: central location for configuration files used to define widget views.

### Typical data flow

A typical widget request flows like:

1. **Request comes in** to a widget route (ex: `/widget_controller/{key}` or landing page AJAX data route).
2. **Path processor normalizes** legacy-style paths into the canonical `{key}` format.
3. **Controller resolves node id** from `{key}`.
4. **Widget configuration is loaded** (often from files/config associated with that node).
5. **Service layer fetches data** via `DataService` + domain repositories.
6. **Repository executes SQL** using infrastructure SQL model factories/utilities.
7. **Presentation layer renders** HTML (Twig template) or JSON (AJAX endpoints).

## Installation

Because this is a suite, installation typically means enabling the individual modules you need.

1. Enable required modules (examples):

- `checkbook_infrastructure_layer`
- `checkbook_domain`
- `checkbook_services`
- `widget_controller`
- `landing_page_widget_view`
- `checkbook_view_configs`

2. Clear caches after enabling:

- Admin UI: Performance page
- Or via Drush (if available): `drush cr`

## Configuration

Configuration is split across several concerns:

- **Widget view configuration**
  - Centralized under the `checkbook_view_configs` module.

- **Datasource awareness**
  - Services commonly use `Datasource::getCurrent()` to scope caching and data selection.

- **Caching**
  - `DataService` caches dataset results and row counts using memcache.
  - Cache keys include datasource and an md5 of parameters + query metadata.

- **URL normalization**
  - `WidgetControllerPathProcessor` rewrites multiple URL patterns to keep legacy links working.

- **Landing page data tables**
  - Uses DataTables assets from `modules/custom/jquery_plugins/data_tables/...` and site-specific JS.

## Usage

### 1) Render a widget via controller

- Visit:
  - `/widget_controller/{key}`

The `{key}` encodes a node id (decoded by `RequestUtil::_getnodeid()`), and the controller returns the widget body content.

### 2) Fetch landing page widget data (AJAX)

- Call:
  - `/checkbook_views/data_tables/ajax_data/node/{key}`

This returns JSON representing a widget’s table data for rendering on the landing page.

### 3) Use the landing page widget Twig template

`table_by_rows.html.twig`:

- Attaches `landing_page_widget_view/landing_page_data_table`.
- Renders table headers/rows from the loaded widget config + returned data.
- Injects widget-specific JS via `landing_page_widget_view_add_js_twig(node)`.

## Testing

### Manual Testing

1. **Scenario 1: Widget controller route renders content**
  - Description: Verify widget markup is returned by the controller.
  - Steps:
    1. Visit `/widget_controller/{key}` for a known widget.
  - Expected Result: The widget body HTML is returned and renders correctly.

2. **Scenario 2: Landing page widget AJAX returns JSON**
  - Description: Verify the landing page data endpoint works.
  - Steps:
    1. Request `/checkbook_views/data_tables/ajax_data/node/{key}`.
  - Expected Result: Response is JSON and includes expected table data.

3. **Scenario 3: Legacy URL path normalization**
  - Description: Ensure old widget URLs continue to resolve.
  - Steps:
    1. Use a legacy-style widget URL that contains extra slashes.
    2. Confirm the request is rewritten and handled.
  - Expected Result: Route resolves (no 404) and widget loads.

4. **Scenario 4: DataTables widget renders**
  - Description: Verify the landing page table template + library.
  - Steps:
    1. Load a page that renders `table_by_rows.html.twig`.
    2. Confirm DataTables JS initializes and table behavior works.
  - Expected Result: Table is interactive (sorting/paging per JS configuration).

### Automated Testing

No automated tests are included in this suite currently.
