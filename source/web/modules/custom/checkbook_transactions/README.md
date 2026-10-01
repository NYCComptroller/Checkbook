# Checkbook Transactions

Provides the Drupal routes and controller glue code for Checkbook “Transactions” pages across multiple domains (Spending, Contracts, Payroll, Budget, Revenue) and multiple datasources (Citywide, OGE/EDC, NYCHA).

This module primarily:

- Defines many transaction page routes.
- Routes those URLs to controllers that load prebuilt transaction pages stored as Drupal nodes.
- Includes an inbound path processor to translate legacy (slash-separated) paths into the current `{params}` format.
- Provides small helpers for titles and “no data” messaging.

## Table of Contents

- [Module Purpose](#module-purpose)
- [Module Functionality](#module-functionality)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Testing](#testing)

## Module Purpose

`checkbook_transactions` exists to support the Checkbook UI pattern where “Transactions” pages are:

- Accessible via a consistent set of URLs (many of which originated in Drupal 7).
- Rendered using Drupal nodes (typically containing widgets/panels or layout-builder content).
- Parameterized by a compact route argument `{params}` (often a colon-separated string) plus query string parameters.

Instead of embedding large amounts of rendering logic in PHP, these controllers generally:

- Resolve a path alias (e.g., `/spending/transactions`).
- Extract the node ID behind that alias.
- Load and render that node.
- Fall back to a known hard-coded node ID if the alias lookup does not return a node.
- If a feature flag / “record exists” check fails, return a “no records” message.

## Module Functionality

### 1) Routes

Defined in `checkbook_transactions.routing.yml`. The module provides a large set of transaction-related routes, grouped broadly as:

- **Budget**
  - `/budget/transactions/budget_transactions/{params}`
  - `/budget/transactions/{params}` (advanced)
  - `/nycha_budget/transactions/{params}` (+ committed/remaining variants)
  - `/nycha_budget/search/transactions/{params}`

- **Revenue**
  - `/revenue/transactions/revenue_transactions/{params}`
  - `/revenue/transactions/{params}` (advanced)
  - `/revenue/agency_details/{params}`
  - `/revenue/revcat_details/{params}`
  - `/revenue/fundsrc_details/{params}`
  - NYCHA variants under `/nycha_revenue/...`

- **Payroll**
  - `/payroll/transactions/{params}`
  - `/payroll/payroll_title/transactions/{params}`
  - `/payroll/monthly/transactions/{params}`
  - `/payroll/agencywide/transactions/{params}`
  - `/payroll/employee/transactions/{params}` and CY variant
  - Advanced search routes under `/payroll/search/...`

- **Spending**
  - `/spending/transactions/{params}`
  - Dashboard variants:
    - `/spending/transactions/dashboard/sp/{params}`
    - `/spending/transactions/dashboard/ss/{params}`
    - `/spending/transactions/dashboard/ms/{params}`
  - Datasource variant:
    - `/spending/transactions/datasource/checkbook_oge/{params}`
  - NYCHA variants under `/nycha_spending/...`
  - Advanced search routes under `/spending/search/...`

- **Contracts / Subcontracts**
  - `/contract_details/{params}`
  - `/subcontract/transactions/{params}`
  - Contract transactions by category/status/datasource/dashboard, plus advanced-search variants.
  - NYCHA contract routes under `/nycha_contracts/...`.

All routes require `_permission: 'access content'`.

### 2) Controllers

Transaction routes map to controllers under `src/Controller/*`:

- `BudgetController`
- `RevenueController`
- `PayrollController`
- `SpendingController`
- `ContractController`

Common controller behavior:

- Calls `RequestUtilities::resetUrl()` at the start of many handlers.
- Performs feature availability checks using `_checkbook_project_recordsExists(<id>)`.
- Loads the transaction page node either by path alias lookup or a fallback hard-coded node ID.
- Returns either:
  - A rendered node view array (`$view_builder->view($node, 'default')`), or
  - A small render array with `#markup` to show “no records” messaging.

### 3) Inbound path processor (legacy URL normalization)

Service definition: `checkbook_transactions.path_processor` in `checkbook_transactions.services.yml`.

- Class: `Drupal\checkbook_transactions\PathProcessor\CheckbookTransactionsPathProcessor`
- Tag: `path_processor_inbound` with priority `250`.

Purpose:

- Converts legacy, slash-separated paths into the colon-separated `{params}` format expected by the routes.
- Implements many special-case rewrites for:
  - Budget
  - Revenue
  - Payroll
  - Spending
  - Contracts and contract spending
  - NYCHA variants

Example behavior pattern:

- Incoming: `/spending/transactions/agency/XYZ/year/2025`
- Rewritten: `/spending/transactions/agency:XYZ:year:2025`

(The exact parameter keys depend on the caller; the path processor generally performs the “`/` to `:`” transformation and preserves path structure.)

### 4) Utilities

- `Drupal\checkbook_transactions\Utilities\TransactionsUtil`
  - Generates titles for multiple transaction pages (Budget/Revenue/Payroll/Spending/Contracts).
  - Provides “no data” title/message helpers such as:
    - `budgetNodataMessage()`
    - `revenueNodataMessage()`
    - `spendingNodataTitle()`
    - `contractNodataTitle()`

These utilities depend heavily on other Checkbook modules (breadcrumbs, widget utilities, date utilities, etc.).

### 5) Custom block

- `Drupal\checkbook_transactions\Plugin\Block\CheckbookCustomContentBlock`

Provides a configurable block that can render a title and body. Notable behaviors:

- Supports a `text_format` field using the `php_code` text format.
- Validates PHP code by running `eval()` in `blockValidate()` when the `php_code` format is selected.

### 6) Layout helper classes

- `TransactionsTowColumns39StackedLayoutClass`
- `TransactionsTowColumns66BricksLayoutClass`

These are alternate layout classes that extend `LayoutDefault` and add configuration fields (`title` and `php` text_format) for layout-builder style layouts.

## Installation

1. Enable the module:

- **Via UI**: Extend page
- **Via Drush** (if available):
  - `drush en checkbook_transactions`

2. Clear caches:

- Admin UI: Performance page
- Or via Drush: `drush cr`

## Configuration

`checkbook_transactions` is primarily configuration-by-content and configuration-by-URL:

- **Transaction pages are stored as nodes**
  - Many controllers load a node by resolving the path alias for a given transactions URL.
  - If alias resolution fails, controllers use hard-coded fallback node IDs.

- **Feature gating / availability**
  - Controllers frequently check `_checkbook_project_recordsExists(<id>)`.
  - If the check fails, the controller returns a “no transactions” message.

- **Inbound path processing**
  - The inbound path processor is registered as a service and runs early (priority `250`).
  - If you add new legacy-style transaction URLs, you may need to extend `CheckbookTransactionsPathProcessor` so the path resolves to the correct route.

## Usage

### 1) Access a transactions page

Common examples:

- Spending transactions:
  - `/spending/transactions/<params>`
- Contract transactions (expense):
  - `/contract/transactions/contcat/expense/<params>`
- Payroll transactions:
  - `/payroll/transactions/<params>`
- Budget transactions:
  - `/budget/transactions/<params>`

`<params>` is a single route parameter. For many pages it is a colon-separated list created from legacy paths.

### 2) Legacy path compatibility

If you have older URLs that include extra path segments, the inbound path processor attempts to normalize them by:

- Removing the route prefix
- Replacing remaining `/` separators with `:`
- Re-inserting the rewritten `{params}` back into the canonical route path

This allows older links to continue working even when the route expects a single `{params}` argument.

### 3) How controllers choose what to render

Most controllers follow this pattern:

- Determine if the page should exist using `_checkbook_project_recordsExists(<id>)`.
- Load the page node:
  - Prefer: `path_alias.manager->getPathByAlias('<known alias>')` then `Node::load(<nid>)`
  - Fallback: hard-coded node ID
- Render the node in the `default` view mode.

If the page is disabled by the record-exists check, they return a small render array with a “no records” message.

## Testing

### Manual Testing

1. **Scenario 1: Route resolves and renders a node**
  - Description: Verify a transactions URL loads the expected node-backed page.
  - Steps:
    1. Visit `/spending/transactions` (or a known transactions route in your environment).
    2. Confirm the page renders and contains expected widgets/content.
  - Expected Result: A node-backed transactions page is rendered.

2. **Scenario 2: Legacy URL rewrite works**
  - Description: Verify inbound path processing converts legacy paths.
  - Steps:
    1. Use a known legacy-style URL with extra slash-separated segments.
    2. Confirm the request resolves to the correct transactions controller.
  - Expected Result: Page loads (no 404) and parameters are preserved via the rewritten `{params}`.

3. **Scenario 3: Feature-gated “no records” behavior**
  - Description: Ensure disabled transaction pages show the expected fallback.
  - Steps:
    1. In an environment where a specific `_checkbook_project_recordsExists(<id>)` returns false, visit the matching route.
  - Expected Result: Page shows a “There are no … transactions.” message rather than erroring.

4. **Scenario 4: Domain coverage smoke test**
  - Description: Validate key routes across domains.
  - Steps:
    1. Visit one route each for Spending, Contracts, Payroll, Budget, Revenue.
  - Expected Result: All resolve and render without PHP errors.

### Automated Testing

No automated tests are included in this module currently.
