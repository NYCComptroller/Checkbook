# Checkbook Smart Search

Smart search module for the Checkbook NYC Drupal site. Provides a type-ahead search box, Solr-backed “smart search” results page, faceted narrowing, and CSV export.

## Table of Contents

- [Module Purpose](#module-purpose)
- [Module Functionality](#module-functionality)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Testing](#testing)

## Module Purpose

`checkbook_smart_search` implements the Checkbook “Smart Search” experience:

- A single search box that queries Solr-backed datasets (“domains” such as Spending, Contracts, Budget, Revenue, Payroll).
- Type-ahead suggestions for both the main search input and for facet narrowing.
- A results experience with left-side transactions and right-side facet filters.
- A CSV export flow for the currently selected domain (capped to a configurable maximum).

This module is used to provide a fast, user-friendly entry point into the Checkbook data without requiring users to construct an advanced search.

## Module Functionality

Key components:

- **Drupal form**
  - `Drupal\checkbook_smart_search\Form\CheckbookSmartSearchForm`
  - Rendered with Twig template `templates/smart-search-form.html.twig`.
  - Uses a custom submit handler `_checkbook_smart_search_submit()` (from `includes/checkbook_smart_search.inc`) to redirect to the results route.

- **Block plugin**
  - `Drupal\checkbook_smart_search\Plugin\Block\CheckbookSmartSearchForm`
  - Allows site builders to place the Smart Search form as a block.

- **Routes / endpoints** (`checkbook_smart_search.routing.yml`)
  - `/smart_search_form`
    - Displays the Smart Search form.
  - `/smart_search/{solr_datasource}`
    - Full results page (HTML) for a given Solr datasource.
  - `/smart_search/autocomplete/{solr_datasource}`
    - Main input autocomplete endpoint.
  - `/solr_autocomplete/{solr_datasource}/{facet}`
    - Facet autocomplete endpoint used by the narrowing UI.
  - `/smart_search/ajax/results/{solr_datasource}`
    - AJAX endpoint for paginated results.
  - `/exportSmartSearch/form`
    - Export dialog markup (used by the JS-driven export UI).
  - `/exportSmartSearch/download/{solr_datasource}`
    - CSV export download endpoint.

- **Controller**
  - `Drupal\checkbook_smart_search\Controller\DefaultController`
  - `_checkbook_smart_search_get_results()`
    - Builds a `CheckbookSolrQuery`, adds facets/intervals defined by `checkbook_solr` config, executes Solr requests, and returns the themed results page.
  - `_checkbook_smart_search_autocomplete_main_input()`
    - Uses Solr “terms” to return categorized suggestions for the main search input.
  - `_checkbook_smart_search_autocomplete()`
    - Delegates to `_checkbook_autocomplete()` (in `includes/checkbook_smart_search.inc`) to return facet suggestions.
  - `_checkbook_smart_search_ajax_results()`
    - Returns just the results markup used during AJAX paging.

- **Twig extension**
  - `Drupal\checkbook_smart_search\Twig\SmartSearchExtension`
  - Provides Twig functions used by `templates/results.html.twig`:
    - `smartSearchResults()`
    - `FacetData()` / `displayFacetData()`
    - `displayAjaxResults()`

- **Theme hooks & templates**
  - `checkbook_smart_search_theme()` registers themes for:
    - Results wrapper: `templates/results.html.twig`
    - Facet filter: `templates/smart_search_filter.html.twig`
    - AJAX results: `templates/ajax-results.html.twig`
    - Domain-specific result fragments:
      - `templates/spending.html.twig`
      - `templates/contracts.html.twig`
      - `templates/budget.html.twig`
      - `templates/revenue.html.twig`
      - `templates/payroll.html.twig`
      - plus NYCHA-specific variants (`templates/nycha_*.html.twig`)

- **Client-side behavior**
  - Library: `checkbook_smart_search/smart_search_autocomplete` (`checkbook_smart_search.libraries.yml`)
  - JS: `js/smart_search.js`
    - Main search box autocomplete.
    - Facet checkbox/radio filtering (builds a `search_term` query string using the `*!*` delimiter).
    - AJAX paging for results.
    - Export dialog + CSV download via `/exportSmartSearch/download/...`.

Dependencies:

- Declared dependency on `checkbook_solr` (Solr query building, facet config, export field config).
- Reuses UI CSS from `checkbook_faceted_search` via `smart_search_results` library (`/modules/custom/checkbook_faceted_search/css/narrow-down-filter.css`).

## Installation

1. Ensure the module dependency is available:

- `checkbook_solr`

2. Enable the module:

- **Via UI**: Extend page
- **Via Drush** (if available):
  - `drush en checkbook_smart_search`

3. Clear caches after enabling:

- Admin UI: Performance page
- Or via Drush: `drush cr`

## Configuration

Most configuration is sourced from existing Checkbook configuration and `checkbook_solr` facet/export definitions.

- **Solr datasource selection**
  - The current datasource is determined by `Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource::getCurrentSolrDatasource()`.
  - The module injects it for JS consumption via:
    - `checkbook_smart_search_page_attachments_alter()`
    - `checkbook_smart_search_form_alter()`

- **Export limits**
  - Export behavior reads from the `check_book` config:
    - `smart_search.export_record_limit` (default fallback: `200000`)
    - `smart_search.export_page_size` (default fallback: `10000`)
  - Implementation: `_checkbook_smart_search_export_data()` in `includes/checkbook_smart_search.inc`.

- **Facet definitions**
  - Facets and intervals are loaded via `CheckbookSolr::getFacetConfigByDatasource($solr_datasource)`.
  - Autocomplete categories for the main search input are loaded via `CheckbookSolr::getAutocompleteTerms($solr_datasource)`.

## Usage

### 1) Display the Smart Search form

You can display the form in either of the following ways:

- **Route**: Visit `/smart_search_form`.
- **Block placement**: Place the block **“Checkbook Smart Search Form Block”**.

The form submits by redirecting to:

- `/smart_search/{solr_datasource}?search_term=<your term>`

Notes:

- The UI and back end both enforce a minimum of **3 characters** for the primary search term.
- The form placeholder text is customized based on datasource (e.g., Citywide vs EDC vs NYCHA) in `checkbook_smart_search_form_alter()`.

### 2) Use autocomplete in the main search input

The search box uses jQuery UI Autocomplete.

- Endpoint:
  - `/smart_search/autocomplete/{solr_datasource}`
- Client behavior:
  - Defined in `js/smart_search.js` under `Drupal.behaviors.smart_search_autocomplete`.

The endpoint returns categorized suggestions; selecting a suggestion navigates directly to a filtered results URL.

### 3) View results and narrow using facets

Results are rendered by the `smart_search_results` theme (`templates/results.html.twig`) which includes:

- **Left column**: Transaction results with pagination.
- **Right column**: “Narrow Down Your Search” facet filters.

Facet selection behavior:

- Clicking facet checkboxes/radios triggers `applySearchFilters()` (in `js/smart_search.js`).
- Filters are encoded into the `search_term` query parameter using the `*!*` delimiter.

Facet autocomplete behavior:

- Facet search inputs call:
  - `/solr_autocomplete/{solr_datasource}/{facet}`

### 4) Export CSV

On the results page, an **export** control opens a modal allowing you to select a single domain to export.

- Export dialog content is assembled client-side.
- Data download is requested from:
  - `/exportSmartSearch/download/{solr_datasource}?search_terms=...&domain=<domain>`

Important behavior:

- Export is capped to the configured maximum (default `200000`).
- Exports are generated by paging through Solr results and assembling CSV server-side.

## Testing

### Manual Testing

1. **Scenario 1: Smart Search form renders**
  - Description: Verify the form route and/or block renders correctly.
  - Steps:
    1. Visit `/smart_search_form` (or place the Smart Search block on a page).
    2. Confirm the search input and Search button render.
  - Expected Result: Form is visible and the placeholder matches the active datasource.

2. **Scenario 2: Search validation (minimum length)**
  - Description: Ensure invalid search terms are blocked.
  - Steps:
    1. Enter 1-2 characters.
    2. Attempt to submit.
  - Expected Result: User is prompted to enter at least 3 characters; no results page is executed.

3. **Scenario 3: Results page renders**
  - Description: Validate core results rendering.
  - Steps:
    1. Search for a known term (3+ characters).
    2. Confirm results show transactions.
  - Expected Result: Results list appears and pagination is available.

4. **Scenario 4: Facet selection updates results**
  - Description: Verify facet narrowing changes the result set.
  - Steps:
    1. Perform a search.
    2. Select one or more facet options in the right column.
  - Expected Result: Page reloads (or updates) with a `search_term` query string containing facet selections; results and counts update.

5. **Scenario 5: Export CSV**
  - Description: Verify export modal and download functionality.
  - Steps:
    1. Perform a search with results.
    2. Click `export`.
    3. Select a domain and click “Download Data”.
  - Expected Result: Browser downloads a `.csv` file; contents match the selected domain.

### Automated Testing

No automated tests are included in this module currently.
