# Checkbook Widget Retroactivity

`checkbook_widget_retroactivity` provides the Drupal presentation layer (routes, controllers, blocks, theme hooks, templates, and front-end assets) for the **Retroactivity Dashboard**.

At a high level, this module:

- Exposes the **Retroactivity dashboard landing page** (node-backed).
- Renders a tabbed dashboard UI (tabs + chart tiles + chart placeholder).
- Provides AJAX endpoints to load:
  - Tab headings.
  - Per-tab chart tile sets.
- Provides a filter form (with autocomplete) that drives Highcharts widgets via AJAX.
- Loads dashboard/tab/tile definitions from JSON configuration files shipped with the module.

## Table of Contents

- [Module Purpose](#module-purpose)
- [Module Functionality](#module-functionality)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Testing](#testing)

## Module Purpose

The Retroactivity Dashboard is implemented as a node-backed Drupal page (so layout/content can be managed in Drupal), but it needs a custom interactive UI:

- Tab navigation (Overview, Contract Volume, Contract Value, etc.).
- A set of “chart tiles” per tab.
- A chart region that loads Highcharts content dynamically.
- A filter form that applies fiscal year ranges and other filters.

`checkbook_widget_retroactivity` provides the supporting PHP + Twig + JS needed to implement that UX, while keeping:

- The *page shell* managed by Drupal content (a node at `/retroactivity`).
- The *dashboard behavior* driven by JSON config and AJAX.

## Module Functionality

### 1) Routes

Defined in `checkbook_widget_retroactivity.routing.yml`.

- **Dashboard landing page**
  - Route: `checkbook_widget_retroactivity.landing`
  - Path: `/retroactivity/{params}` (defaults `params: ''`)
  - Controller: `RetroactivityWidgetController::contractVolume()`

- **Filter form endpoint**
  - Route: `checkbook_widget_retroactivity.filter_form`
  - Path: `/retroactivity-filter`
  - Form: `RetroactivityChartFilterForm`

- **AJAX: Tab headings**
  - Route: `checkbook_widget_retroactivity.tab_header`
  - Path: `/retroactivity-tabs/ajax/{tab_id}`
  - Controller: `DashboardAjaxController::loadTabHeader()`

- **AJAX: Chart tiles**
  - Route: `checkbook_widget_retroactivity.load_tiles`
  - Path: `/retroactivity-tiles/ajax/{tab_id}`
  - Controller: `DashboardAjaxController::loadTiles()`

All routes require `_permission: 'access content'`.

### 2) Node-backed landing page controller

`src/Controller/RetroactivityWidgetController.php`

- Resolves the path alias `/retroactivity` to a node ID.
- Loads and renders that node in the `default` view mode.
- If alias resolution fails, falls back to a hard-coded node ID `1126`.

### 3) AJAX controller

`src/Controller/DashboardAjaxController.php`

- `loadTabHeader($tab_id)`
  - Loads tab configuration (JSON) and returns a rendered fragment via `AjaxResponse` + `HtmlCommand`.
  - Theme hook used: `retroactivity_tab_header`.

- `loadTiles($tab_id)`
  - Instantiates the block plugin `retroactivity_chart_tiles_block` with `tab_id` injected into block configuration.
  - Returns the rendered block via `AjaxResponse` + `HtmlCommand`.

Note: `DashboardAjaxController::getDashboardConfig()` attempts to read `config/dashboard_config.json`. (The module also ships `src/Config/retroactivity-dashboard-mapping.json` which is used elsewhere for the tab/tile definitions.)

### 4) Blocks

Block plugins under `src/Plugin/Block/`:

- `retroactivity_tabs_block` (`RetroactivityTabsBlock`)
  - Builds the tab list from `src/Config/retroactivity-dashboard-mapping.json`.
  - Filters to enabled tabs and sorts by the configured `order`.
  - Skips rendering within Layout Builder admin routes (`layout_builder.*`).
  - Attaches libraries and passes config to `drupalSettings`.

- `retroactivity_chart_tiles_block` (`RetroactivityChartTilesBlock`)
  - Reads the same JSON config.
  - Uses the current `tab_id` route parameter to decide which tiles to display.
  - Filters to enabled tiles, sorts by `order`, and marks the first as active.

- `retroactivity_chart_placeholder` (`RetroactivityChartPlaceholderBlock`)
  - Renders a themed placeholder container where Highcharts content is injected.
  - Attaches JS/CSS libraries needed for the chart area.

- `widget_retroactivity_filter` (`WidgetRetroactivityFilterBlock`)
  - Embeds `RetroactivityChartFilterForm` as a block.
  - Skips rendering within Layout Builder admin routes.

### 5) Theme hooks and Twig templates

`checkbook_widget_retroactivity.module` defines theme hooks:

- `retroactivity_tabs`
  - Template: `templates/retroactivity-tabs.html.twig`

- `retroactivity_tab_header`
  - Template: `templates/retroactivity-tab-header.html.twig`

- `retroactivity_chart_tiles`
  - Template: `templates/retroactivity-chart-tiles.html.twig`

- `retroactivity_chart_placeholder`
  - Template: `templates/retroactivity-chart-placeholder.html.twig`

### 6) JSON configuration

The primary dashboard mapping file is:

- `src/Config/retroactivity-dashboard-mapping.json`

This file defines:

- A global dashboard heading (`heading`).
- Whether filters should be retained when switching tabs (`retainFilters`).
- `tabs`:
  - Label, enabled status, sort order, and which chart tiles belong to the tab.
- `chart_tiles`:
  - Label, enabled status, sort order, thumbnail image path, and `chart_id` used by JS.
  - Some tiles define `exclude_filters` (ex: excluding `edit-year-from`).

### 7) Front-end assets and Highcharts integration

Libraries are defined in `checkbook_widget_retroactivity.libraries.yml`.

Key library used across the dashboard:

- `checkbook_widget_retroactivity/retroactivity-form`
  - CSS:
    - `css/retroactivity-chart.css`
    - `css/retroactivity-form.css`
    - `css/retroactivity-tabs.css`
    - `css/retroactivity-tiles.css`
  - JS:
    - Highcharts scripts loaded from `/modules/custom/widget_framework/widget_highcharts/...`
    - `js/retroactivity-ajax.js`
  - Dependencies include core jQuery/Drupal libraries, plus:
    - `widget_highcharts/highcharts`

The primary behavior file is:

- `js/retroactivity-ajax.js`

Notable behavior:

- Manages dashboard tab switching and tile click handling.
- Builds a **widget AJAX URL** of the form:
  - `/widget/{chartId}/retroactivity/year-end/{...}/...`
  - This module constructs the URL and injects response HTML into the chart container.
  - The `/widget/...` endpoint itself is provided elsewhere (outside this module).
- Adds jQuery UI autocomplete for Agency and Vendor inputs.
  - Autocomplete source uses `/advanced_autocomplete/{solr_datasource}/{facet}/?search_term=...`.
  - That endpoint is provided elsewhere (outside this module).

## Installation

1. Enable the module:

- **Via UI**: Extend page
- **Via Drush** (if available):
  - `drush en checkbook_widget_retroactivity`

2. Clear caches:

- Admin UI: Performance page
- Or via Drush: `drush cr`

## Configuration

This module is driven primarily by:

- **Dashboard mapping JSON**
  - `src/Config/retroactivity-dashboard-mapping.json`
  - Controls:
    - Which tabs/tiles are enabled.
    - Display order.
    - Chart IDs and tile thumbnails.
    - Per-chart filter exclusions (ex: hide “year-from”).

- **Node backing the `/retroactivity` landing page**
  - The controller resolves `/retroactivity` via the alias system.
  - If the alias is missing, the controller falls back to node ID `1126`.

- **External dependencies (endpoints/assets provided by other modules)**
  - Highcharts assets referenced under `modules/custom/widget_framework/widget_highcharts/...`.
  - Widget HTML endpoint referenced by JS at `/widget/{chartId}/retroactivity/...`.
  - Autocomplete endpoint referenced by JS at `/advanced_autocomplete/...`.

## Usage

### 1) Visit the Retroactivity Dashboard

- Navigate to:
  - `/retroactivity`

This renders the node-backed page and relies on blocks/templates/JS to provide the dashboard UI.

### 2) Interact with tabs and tiles

- Tabs are rendered by the `Retroactivity Dashboard Tabs` block (`retroactivity_tabs_block`).
- Selecting a tab triggers an AJAX call to:
  - `/retroactivity-tiles/ajax/{tab_id}`
- Selecting a tile calls `updateChartWithFilters(chartId)` to load the chart widget content.

### 3) Apply filters

The `Widget Retroactivity Filter` block embeds `RetroactivityChartFilterForm`.

Filters include:

- Fiscal year range (`year-from`, `year-to`)
- Agency (autocomplete)
- Vendor (autocomplete)
- M/WBE category (checkboxes)
- Industry (checkboxes)
- Non-profit status (checkboxes)

The form does not submit a traditional page request; instead, JS collects the selected filters and requests widget HTML via the `/widget/...` endpoint.

## Testing

### Manual Testing

1. **Scenario 1: Landing page renders via alias**
  - Description: Ensure `/retroactivity` resolves to a node and renders.
  - Steps:
    1. Visit `/retroactivity`.
  - Expected Result: A rendered node page appears (no 404).

2. **Scenario 2: Tabs render and are ordered/enabled per JSON**
  - Description: Ensure tabs are filtered/sorted using `retroactivity-dashboard-mapping.json`.
  - Steps:
    1. Load `/retroactivity`.
    2. Confirm only enabled tabs appear and in the configured order.
  - Expected Result: Tab list matches JSON.

3. **Scenario 3: Tiles load via AJAX**
  - Description: Clicking a non-overview tab loads chart tiles.
  - Steps:
    1. Click “Contract Volume” tab.
  - Expected Result: Tiles HTML is injected into `.retroactivity-tiles-container`.

4. **Scenario 4: Chart loads via widget endpoint**
  - Description: Clicking a tile loads the chart.
  - Steps:
    1. Click the first tile under a tab.
  - Expected Result: JS requests `/widget/{chartId}/retroactivity/...` and injects the returned chart markup into the chart container.

5. **Scenario 5: Autocomplete works for Agency/Vendor**
  - Description: Form autocomplete calls the configured endpoint.
  - Steps:
    1. Type 3+ characters in Agency or Vendor fields.
  - Expected Result: Suggestions appear; selecting one populates the visible field and the hidden `*-selected` field.

### Automated Testing

No automated tests are included in this module currently.
