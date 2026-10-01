# Checkbook Solr Client

Provides the core Solr client and query-building utilities used across the Checkbook NYC Drupal codebase. This module centralizes:

- Solr connection URL selection per datasource (Citywide / EDC / NYCHA).
- Smart Search facet configuration (JSON)
- Query string construction for Solr `select` and `terms` requests.
- Convenience APIs for autocomplete and option lists.
- A small route for returning Solr-powered option lists as JSON.

## Table of Contents

- [Module Purpose](#module-purpose)
- [Module Functionality](#module-functionality)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Testing](#testing)

## Module Purpose

`checkbook_solr` is the foundational “Solr integration” module for Checkbook NYC.

It exists to keep Solr access consistent and reusable across other custom modules (for example `checkbook_smart_search`, advanced search, faceted search, alerts, etc.). Instead of every module hand-assembling Solr queries and loading per-datasource facet definitions, this module provides:

- A single client class (`CheckbookSolr`) responsible for calling Solr and handling simple caching.
- A query builder (`CheckbookSolrQuery`) that translates Checkbook UI parameters into Solr query parameters (`q`, `fq`, facets, intervals, paging, sort, wt).
- JSON configuration files that define facets, export fields, and autocomplete term categories.

## Module Functionality

### Solr client (`Drupal\checkbook_solr\CheckbookSolr`)

- Selects the correct Solr base URL based on `$datasource`.
  - Citywide maps to config key `check_book.solr.url`.
  - EDC maps to `check_book.solr_edc.url`.
  - NYCHA maps to `check_book.solr_nycha.url`.
- Executes requests using `file_get_contents()`.
- Supports:
  - `request_phps()` (expects Solr response format `wt=phps` and `unserialize()`s it)
  - `request_csv()` (raw CSV string)
  - `requestTerms()` (Solr terms handler)
- Adds basic caching using `_checkbook_dmemcache_get()` / `_checkbook_dmemcache_set()` keyed by datasource + md5(query).

### Query builder (`Drupal\checkbook_solr\CheckbookSolrQuery`)

- Extends `CheckbookSolrQueryBase` and builds Solr query strings.
- Consumes and applies JSON config via helper methods on `CheckbookSolr`:
  - Facets: `getFacetConfigByDatasource($solr_datasource)`
  - Sort: `getSortConfig($solr_datasource)`
  - Parameter mapping: `getParamMapping()`
  - Autocomplete mapping: `getAutocompleteMapping()`
- Core responsibilities:
  - Parse the Checkbook “smart search” query string format using the `*!*` delimiter.
  - Convert terms into:
    - `q` (search query)
    - `fq` (filter queries)
    - facet requests, including tagged facets and interval facets
    - paging (`start`/`rows` from `page` + `rows`)
  - Provide special handling for known parameters (examples: vendor type mapping, contract status mapping, spending category mapping, year mapping).

### Base query behavior (`Drupal\checkbook_solr\CheckbookSolrQueryBase`)

- Stores query state (`q`, `fq`, `facets`, `intervals`, `rows`, `page`, `sort`, `wt`, etc.).
- Handles:
  - `facet=true` payload generation with `facet.mincount`, `facet.sort`, `facet.limit`.
  - Tagged facets (`tagFacets()`) used so “unchecked facet counts” can be computed by excluding the currently selected facet.
  - Interval facets via `facet.interval` + `f.<facet>.facet.interval.set`.
  - Query construction rules (if `q` is not `*:*` and not already a `field:...` query, it defaults to `q=text:<q>`).

### JSON configuration (`src/Config/*.json`)

Facet, search-result, export-field, and autocomplete term definitions are per datasource:

- `src/Config/citywide.json`
- `src/Config/edc.json`
- `src/Config/nycha.json`

Shared mappings:

- `src/Config/parameter.json`
  - Maps “UI parameter names” to underlying Solr facet field names.
- `src/Config/autocomplete.json`
  - Maps facet fields to their “*_autocomplete” Solr fields.

### “Guru” option helpers (`Drupal\checkbook_solr\Guru\*`)

These classes provide a convenient way to fetch “all values” for a facet (optionally filtered by domain and other `fq`s):

- `CheckbookGuru`
  - Runs facet queries sorted by index and returns de-duplicated facet values.
  - Supports parsing values formatted like `Title[ID]`.
- `CheckbookGuruOptionsLabels`
  - Returns an array of `['label' => ..., 'value' => ..., 'code' => ...]` entries when the facet values include bracketed codes.
- `CheckbookGuruOptionsAttributes`
  - Returns `options` + `options_attributes` arrays suitable for building form selects.

Module-level helper functions are exposed from `checkbook_solr.module`:

- `checkbook_solr_options_labels($data_source, $domain, $facet, $filters = [])`
- `checkbook_solr_options_attributes($data_source, $domain, $facet, $filters = [])`

### Routes / endpoints

Defined in `checkbook_solr.routing.yml`:

- `/solr_options/{data_source}/{domain}/{facet}`
  - Controller: `Drupal\checkbook_solr\Controller\DefaultController::checkbook_print_solr_options_json`
  - Returns JSON options for the given facet.
  - Query string filters are accepted (and sanitized via `UrlHelper::filterQueryParameters`).
  - If a `term` is provided and no matches exist, the endpoint returns `No Matches Found`.

## Installation

1. Enable the module:

- **Via UI**: Extend page
- **Via Drush** (if available):
  - `drush en checkbook_solr`

2. Clear caches:

- Admin UI: Performance page
- Or via Drush: `drush cr`

## Configuration

### 1) Solr connection URLs

`CheckbookSolr` reads the Solr base URL from a global `$config` structure:

- Citywide: `$config['check_book']['solr']['url']`
- EDC: `$config['check_book']['solr_edc']['url']`
- NYCHA: `$config['check_book']['solr_nycha']['url']`

If a URL is missing, the module logs a warning.

### 2) Datasource JSON configurations

Datasource config is loaded from `src/Config/<datasource>.json`.

- For Smart Search style use cases, these JSON files define:
  - `facets`
  - `search_results_fields`
  - `export_fields`
  - `autocomplete_terms`
  - `sort.sort_by`

### 3) Parameter mapping

The module maps incoming parameters to Solr field names using `src/Config/parameter.json`.

Example: `domains` -> `domain`, `vendor_names` -> `vendor_name`.

### 4) Autocomplete mapping

Facet autocomplete uses `src/Config/autocomplete.json` to map a facet field to its dedicated autocomplete field.

Example: `vendor_name` -> `vendor_name_autocomplete`.

### 5) Caching

`CheckbookSolr` caches some small responses (under ~100KB) in memcache using:

- `_checkbook_dmemcache_get()`
- `_checkbook_dmemcache_set()`

This assumes the Checkbook memcache integration is available in the runtime.

## Usage

### 1) Build and execute a standard Solr `select` query

Typical usage pattern:

- Create `CheckbookSolrQuery` with datasource, search terms, rows, page.
- Add facets / intervals as needed.
- Execute via `CheckbookSolr::getInstance($datasource)->request_phps('select/?' . $query->buildQuery())`.

This is how `checkbook_smart_search` constructs Smart Search results.

### 2) Smart Search query-string format (`*!*`)

`CheckbookSolrQuery` expects Smart Search-style input strings like:

- `<keyword>`
- `<keyword>*!*domain=spending`
- `<keyword>*!*domain=spending*!*vendor_name=ACME%20INC`

The first segment becomes the main keyword; subsequent segments become `fq` filters and are also recorded as `selectedFacets`.

### 3) Facet autocomplete queries

To build an autocomplete query for a facet:

- Call `setFqAutocompleteTerm($facet, $term)` on a query.
  - This sets `q` to use the appropriate `*_autocomplete` field.
  - It adds the facet back as a requested facet field.
  - It lowers the facet limit for suggestions.
  - It unsets `sort` to avoid irrelevant ordering during autocomplete.

This is used by smart search narrowing and advanced search autocomplete.

### 4) Fetching options via `/solr_options/...`

You can retrieve Solr-driven options from the HTTP endpoint:

- `/solr_options/<data_source>/<domain>/<facet>?term=<partial>`

The controller delegates to `checkbook_solr_options_labels()`.

This endpoint is commonly used to populate select lists / autocomplete lists consistently across modules.

## Testing

### Manual Testing

1. **Scenario 1: Solr options endpoint returns JSON**
  - Description: Verify `/solr_options/...` returns a JSON response.
  - Steps:
    1. Visit `/solr_options/citywide/spending/vendor_name`.
  - Expected Result: Response is valid JSON.

2. **Scenario 2: Autocomplete “No Matches Found” behavior**
  - Description: Verify `term` handling.
  - Steps:
    1. Visit `/solr_options/citywide/spending/vendor_name?term=zzzzzzzz`.
  - Expected Result: Response contains a `No Matches Found` entry.

3. **Scenario 3: Smart Search integration sanity check**
  - Description: Validate downstream modules still function.
  - Steps:
    1. Perform a Smart Search (module: `checkbook_smart_search`).
    2. Use facet narrowing and confirm counts update.
  - Expected Result: Smart Search results and facets work, indicating queries + facet config are loading correctly.

4. **Scenario 4: CSV export query formatting**
  - Description: Validate that `wt=csv` queries can be executed.
  - Steps:
    1. Trigger a Smart Search export download.
  - Expected Result: Export succeeds; CSV records use the configured field list.

### Automated Testing

No automated tests are included in this module currently.
