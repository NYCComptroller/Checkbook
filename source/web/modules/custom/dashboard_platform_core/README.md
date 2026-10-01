# Dashboard Platform Core

`dashboard_platform_core` is a suite of foundational Drupal modules that provide a reusable **data access and query framework** (the “Data Controller”) plus supporting adapters for:

- Datasources (generic SQL + PostgreSQL)
- Caching (Memcached integration)
- Metamodel loading (from JSON files or Drupal DB configuration)
- Joining results (join controller)
- Rendering nodes via AJAX without full page theming (ajaxnode)

This directory is **not a single Drupal module**—it contains multiple related modules grouped under the `REI Dashboard Platform (Core)` / `REI Dashboard Platform Core` packages.

## Table of Contents

- [Module Purpose](#module-purpose)
- [Module Functionality](#module-functionality)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Testing](#testing)

## Module Purpose

The Dashboard Platform Core provides a consistent, modular way to:

- Define datasets/cubes in a **metamodel**.
- Query those datasets/cubes through a **controller** API.
- Plug in different **datasource implementations**.
- Apply reusable **operators** (filters) and **datatype handlers**.
- Enable **caching** through an adapter layer.
- Combine result sets using **join operations**.

In the Checkbook ecosystem, this framework is used as a generic backend for widgets/dashboards that need to request data in a structured way without hardcoding SQL throughout the application.

## Module Functionality

## Included modules

### 1) `data_controller` (Core)

- Name: `Data Controller`
- Description: “Generic functionality to retrieve data from various data sources”
- Services:
  - Registers an event subscriber (`Drupal\data_controller\EventSubscriber\InitSubscriber`) which initializes environment configuration via `Environment::getInstance()`.

Key responsibilities (from `DefaultDataQueryController`):

- Cleans/adjusts inputs.
- Wraps inputs into request objects.
- Prepares call context.
- Loads dataset/cube metadata on-demand.
- Executes dataset/cube queries and count requests through datasource query handlers.

Key API entry points (procedural helpers in `data_controller.module`):

- `data_controller_get_instance()`
  - Returns the singleton `DataQueryControllerProxy::getInstance()`.

- `data_controller_get_metamodel()`
  - Returns `MetaModelFactory::getInstance()->getMetaModel()`.

- `data_controller_get_environment_metamodel()`
  - Returns `EnvironmentMetaModelFactory::getInstance()->getMetaModel()`.

Extensibility via hook-like registries (invoked through `
\Drupal::moduleHandler()->invokeAll()` in various factories):

- `dc_cache` – cache handler registration
- `dc_data_type` – datatype handler registration
- `dc_datasource_operator` – datasource operator registration
- `dc_metamodel_loader` – metamodel loader registration
- `dc_datasource` / datasource factory hooks – datasource registration

Operators and datatype handlers are registered by returning arrays keyed by operator/type names.

### 2) Datasource modules

#### `data_controller_sql` (Core)

- Name: `Core ANSI SQL Database API implementation`
- Depends on: `data_controller`
- Adds SQL-specific logging listeners via `data_controller_datasource_sql_dc_log_message_listener()`.

#### `data_controller_postgresql` (Adapter)

- Name: `PostgreSQL integration`
- Depends on: `data_controller_sql`
- Registers datasource type and query handler mappings:
  - `data_controller_postgresql_dc_datasource()`
  - `data_controller_postgresql_dc_datasource_query()`

These functions wire PostgreSQL-specific formatting/connection extensions into the generic SQL datasource/query handlers.

### 3) Caching adapter

#### `data_controller_memcached` (Adapter)

- Name: `Memcached integration`
- Depends on: `data_controller`

Adds:

- A memcached cache handler via `data_controller_memcached_dc_cache()`.
- An environment metamodel generator/loader via `data_controller_memcached_dc_metamodel_environment_loader()`.

### 4) Metamodel loaders

#### `data_controller_metamodel_file` (Adapter)

- Name: `Meta Model in json files`
- Depends on: `data_controller`

Registers a metamodel loader:

- `data_controller_metamodel_file_dc_metamodel_loader()` → `Drupal\data_controller_metamodel_file\FileMetaModelLoader`

#### `data_controller_metamodel_drupal_database` (Adapter)

- Name: `Environment Meta Model using Drupal database configuration`
- Depends on: `data_controller`

Intended to generate/load environment metamodel configuration from Drupal’s database connection configuration.

### 5) Join support

#### `join_controller` (Core)

- Name: `Join Controller`
- Depends on: `data_controller`

Provides a join controller factory that selects a join handler by method name.

Procedural helpers (in `join_controller.module`):

- `join_controller_get_instance($method)`
  - Returns a handler from `JoinControllerFactory::getInstance()->getHandler($method)`.

- `join_controller_get_supported_methods()`
  - Returns supported join methods.

Join method handlers are registered via the `jc_method` registry function (`join_controller_jc_method()`), with built-in method types including:

- inner
- left outer
- right outer
- full
- cross
- union

### 6) AJAX node rendering

#### `ajaxnode`

- Name: `AJAX Node`
- Route:
  - `/ajaxnode/{node}`
  - Controller: `Drupal\ajaxnode\Controller\AjaxNodeController::ajaxNodeView()`

Purpose:

- Render a node’s `content` and return it without full page/node theming.

## Installation

Since `dashboard_platform_core` is a suite, you enable only the modules you need.

Common enable sets:

- **Minimal data querying**
  - `data_controller`

- **SQL-backed data querying**
  - `data_controller`
  - `data_controller_sql`
  - `data_controller_postgresql` (if using PostgreSQL)

- **With caching**
  - `data_controller_memcached`

- **With metamodel loaded from files**
  - `data_controller_metamodel_file`

- **With join support**
  - `join_controller`

- **AJAX node rendering**
  - `ajaxnode`

After enabling modules, clear caches:

- Admin UI: Performance page
- Or via Drush (if available): `drush cr`

## Configuration

### 1) Environment configuration

`data_controller` initializes `Environment::getInstance()` on request.

`Environment` reads configuration from Drupal’s global `$conf` under the section name:

- `Dashboard Platform`

and also uses:

- `$conf['site_default_country']` (defaults to `us` if not present)

### 2) Metamodel

A metamodel describes available datasets/cubes and their metadata.

- Core API: `data_controller_get_metamodel()`.
- Loaders are provided by adapter modules implementing `dc_metamodel_loader`.

Examples:

- `data_controller_metamodel_file` loads metamodel from JSON files.
- `data_controller_metamodel_drupal_database` generates environment metamodel from Drupal database configuration.

### 3) Datasource types

Datasource adapters register types and query handler mappings using registries such as:

- `dc_datasource`
- `dc_datasource_query`

For PostgreSQL, `data_controller_postgresql` registers `PostgreSQLDataSource::TYPE` and wires in formatting/connection extensions.

### 4) Caching

Caching is provided via the `dc_cache` registry.

- Default local cache: `InMemoryCacheHandler`
- Shared cache adapter: `MemcachedHandler` (via `data_controller_memcached`)

## Usage

### 1) Obtain a Data Controller instance

Use the procedural helper:

- `data_controller_get_instance()`

This returns a `DataQueryController` implementation (via `DataQueryControllerProxy`).

### 2) Query a dataset or cube

The `DataQueryController` interface supports:

- `queryDataset($datasetName, $columns, $parameters, $orderBy, $startWith, $limit, $resultFormatter)`
- `queryCube($cubeName, $columns, $parameters, $orderBy, $startWith, $limit, $resultFormatter)`
- Count equivalents:
  - `countDatasetRecords(...)`
  - `countCubeRecords(...)`

Internally, `DefaultDataQueryController`:

- Trims input.
- Ensures dataset/cube metadata is loaded.
- Prepares datasource query requests.
- Delegates to the datasource query handler.

### 3) Join results

To join two sources:

- Resolve a join handler:
  - `join_controller_get_instance($method)`

- Call the handler’s `join()` method with two `JoinController_SourceConfiguration` objects.

### 4) Render nodes via AJAX

- Request:
  - `/ajaxnode/{node}`

The controller renders `$node->content` via Drupal’s renderer service.

## Testing

### Manual Testing

1. **Scenario 1: `ajaxnode` route renders node content**
  - Steps:
    1. Visit `/ajaxnode/{node}` for a known node.
  - Expected Result: Response contains rendered node content without full page theming.

2. **Scenario 2: Data Controller environment initialization**
  - Steps:
    1. Load any page with `data_controller` enabled.
    2. Confirm no exceptions related to locale/default country.
  - Expected Result: `Environment::getInstance()` initializes successfully.

3. **Scenario 3: Metamodel loader is active**
  - Steps:
    1. Enable `data_controller_metamodel_file` (or another loader).
    2. Exercise any code path that calls `data_controller_get_metamodel()`.
  - Expected Result: Metamodel is populated and can resolve datasets/cubes.

4. **Scenario 4: Datasource adapter works (PostgreSQL)**
  - Steps:
    1. Enable `data_controller_sql` + `data_controller_postgresql`.
    2. Execute a dataset query through `data_controller_get_instance()`.
  - Expected Result: Query executes through SQL datasource handler without errors.

### Automated Testing

No automated tests are included in this suite currently.
