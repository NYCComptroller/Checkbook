# Widget Framework

Provides the core infrastructure for loading, rendering, and serving “widgets” across the Checkbook NYC platform (charts, tables, and custom visualizations driven by JSON configurations).

## Table of Contents

- [Module Purpose](#module-purpose)
- [Module Functionality](#module-functionality)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Testing](#testing)

## Module Purpose

The `widget_framework` module suite is the foundation for dynamic, configuration-driven widgets in Checkbook NYC.

At runtime, widgets are identified by a `{key}` in the URL, mapped to a JSON configuration file, transformed into a PHP configuration object, used to query data via the Dashboard Platform `data_controller`, and then rendered via the appropriate widget implementation module (for example Highcharts or DataTables).

## Module Functionality

The `widget_framework` directory contains multiple Drupal modules that work together:

```
widget_framework/
├── widget/                  # Core widget runtime + routing + JSON config parsing
├── widget_highcharts/       # Highcharts widget type implementation
├── widget_data_tables/      # DataTables widget type implementation and AJAX endpoints
├── widget_services/         # Domain/service helpers used by widgets
└── widget_phpparser/        # Parser utilities for PHP snippets embedded in configs
```

Key capabilities implemented by the suite:

- **Widget routing and request entry points**
  - Core routes are provided by the `widget` module (`widget/widget.routing.yml`):
    - `/widget/{key}`
    - `/widget_ajax_data/node/{node}`
    - `/featuredtrends/node/{key}`
    - `/trends-landing/trends/node/{key}`
  - Widget controllers resolve the `{key}` and return either a rendered theme/template or the widget body markup.

- **JSON configuration loading and parsing**
  - `_widget_node_load_file($key)` loads the widget JSON using `WidgetUtil::getWidgetJsonPath($key)` (JSON typically lives in the `widget_config` module).
  - `widget_config($node)` parses JSON into `$node->widgetConfig` using `Json2PHPObject`, and supports embedded JS functions via a `<function>...</function>` extraction step.
  - `widget_merge_parent_node_config()` supports parent/child config inheritance using `parentNid`.

- **Widget lifecycle orchestration**
  - `widget_node_view($node)` coordinates:
    - `widget_prepare($node)` (URL parameter parsing + default/additional parameters)
    - `widget_data($node)` / `load_widget_data()` (dataset or cube querying through `data_controller`)
    - `widget_invoke($node, 'widget_view')` to delegate rendering to the module that implements the widget type
  - Many JSON-defined steps are evaluated at runtime via `eval()`, for example:
    - `adjustParameters`, `adjustColumns`, `transformationPHP`
    - `widgetPreprocessJSON`, `preProcessConfiguration`, `widgetUpdateJSONConfig`

- **Data access integration (Dashboard Platform)**
  - The core `widget` module depends on `data_controller` and uses it to query datasets/cubes.
  - The system also supports multi-series joins via `join_controller` concepts when a widget configuration defines a `model->join`.

- **Widget type extension mechanism**
  - Widget “types” are discovered via `hook_widget_metadata()` from modules and invoked via `widget_invoke()`.

## Installation

This is a module suite directory containing multiple Drupal modules. At minimum, sites that render widgets need the core `widget` module enabled.

### Dependencies

- `widget` module:
  - `data_controller`
  - `ajaxnode`
- Additional widget types:
  - `widget_highcharts` depends on `widget`
  - `widget_data_tables` depends on `data_controller`

### Enable

Enable the modules required for your use case (examples):

- Core widget runtime:
  - `drush en widget`
- Highcharts widgets:
  - `drush en widget_highcharts`
- DataTables widgets:
  - `drush en widget_data_tables`

After enabling, clear caches:

- `drush cr`

## Configuration

This suite is primarily configured through widget JSON files (typically provided by the `widget_config` module), not through a single UI.

Common configuration concepts used by the runtime include:

- **`widgetType`**
  - Determines which module is responsible for rendering (`widget_invoke()` delegates to the widget type’s implementation module).
- **`cleanURLParameters` / `urlParamMap`**
  - Controls which URL path segments become request parameters and how they map to dataset parameters.
- **Dataset/cube query settings**
  - `dataset`, `columns`, `orderBy`, `limit`, etc.
- **Runtime hooks (executed via `eval()`)**
  - `adjustParameters`, `adjustColumns`, `transformationPHP`, and other optional hook-like config keys.

## Usage

### Rendering a widget by key

The primary entry point is the `widget.node_view_page` route:

- `/widget/{key}`

The `{key}` is resolved to a JSON configuration file (via `WidgetUtil::getWidgetJsonPath()`), then loaded and rendered.

### Request lifecycle (high level)

1. **Route request**: `/widget/{key}`
2. **Controller**: `Drupal\widget\Controller\DefaultController::_widget_node_view_page()`
3. **Load JSON**: `_widget_node_load_file($id)` reads JSON into `$node->widget_json`
4. **Parse config**: `widget_config($node)` populates `$node->widgetConfig`
5. **Prepare**: `widget_prepare($node)` builds `$node->widgetConfig->requestParams` from the URL + defaults
6. **Query**: `widget_data($node)` / `load_widget_data($node)` queries `data_controller`
7. **Transform**: `transformationPHP` may post-process `$node->data`
8. **Render**: `widget_invoke($node, 'widget_view')` delegates output generation

### AJAX endpoints

The suite includes routes used by widget implementations to load data asynchronously:

- `/widget_ajax_data/node/{node}` (core widget AJAX endpoint)
- `/dashboard_platform/data_tables/ajax_data/node/{node}` (DataTables)
- `/dashboard_platform/data_tables_list/ajax_data/node/{node}` (DataTables list)
- `/gridview/popup/widget/{key}` (Highcharts grid view popup)

## Testing

### Manual Testing

1. **Scenario 1: Widget renders by key**
  - Description: Verify a widget JSON can be resolved and rendered.
  - Steps:
    1. Visit `/widget/{key}` for a known-good widget key.
    2. Confirm the response contains widget markup (chart/table/card).
  - Expected Result: Widget renders without PHP errors and displays data.

2. **Scenario 2: URL parameters affect dataset query**
  - Description: Verify URL path parameters are parsed into `requestParams` and affect results.
  - Steps:
    1. Visit a widget with no parameters.
    2. Visit the same widget with supported URL parameters (per `cleanURLParameters`).
  - Expected Result: Output changes consistently with the parameters.

3. **Scenario 3: Widget type delegation**
  - Description: Confirm widget rendering is handled by the correct implementation module.
  - Steps:
    1. Identify the widget’s `widgetType` in its JSON.
    2. Verify the output corresponds to that widget type (e.g., Highcharts vs DataTables).
  - Expected Result: The correct renderer is used and required JS/CSS assets load.

### Automated Testing

No dedicated automated tests were identified in this module suite directory. Testing is typically performed via integration/manual verification in environments with representative widget JSON configurations enabled.
