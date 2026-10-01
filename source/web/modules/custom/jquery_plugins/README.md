# jQuery Plugins

`jquery_plugins` is a utility module whose primary purpose is to provide a central, Drupal-managed home for **front-end vendor assets** (primarily jQuery plugins) that other custom modules can attach via Drupal libraries.

This module does not define routes, controllers, or business logic. It is essentially an asset bundle exposed through `jquery_plugins.libraries.yml`.

## Table of Contents

- [Module Purpose](#module-purpose)
- [Module Functionality](#module-functionality)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Testing](#testing)

## Module Purpose

Many Checkbook NYC custom modules rely on older or specialized jQuery plugins (scroll panes, chosen/select widgets, custom scrollbars, pagination, DataTables, etc.).

`jquery_plugins` standardizes where these assets live and how they are attached by:

- Providing Drupal libraries with stable machine names.
- Ensuring dependencies (like `system/jquery`) are declared.
- Making it easier for other modules to attach these assets through:
  - `#attached['library']`
  - `attach_library()` in Twig

## Module Functionality

### 1) Module metadata

- File: `jquery_plugins.info.yml`
- Name: `jQuery plugins`
- Description: “A Home for miscellaneous jQuery plugins.”

### 2) Libraries

Libraries are defined in:

- `jquery_plugins.libraries.yml`

The module currently defines these libraries:

#### `jquery_plugins/jScrollPane`

- Purpose: custom scroll pane widget.
- JS:
  - `js/jquery.jscrollpane.min.js`
  - `js/jquery.mousewheel.js`
- CSS:
  - `css/jquery.jscrollpane.css`
- Dependencies:
  - `system/jquery`

#### `jquery_plugins/chosen`

- Purpose: searchable dropdown (Chosen).
- JS:
  - `jquery.searchabledropdown/chosen.jquery.js`
- CSS:
  - `jquery.searchabledropdown/chosen.css`
- Dependencies:
  - `system/jquery`

#### `jquery_plugins/custom-scrollbar`

- Purpose: Malihu custom content scroller.
- JS:
  - `malihu-custom-scrollbar-plugin-master/jquery.mCustomScrollbar.concat.min.js`
- CSS:
  - `malihu-custom-scrollbar-plugin-master/jquery.mCustomScrollbar.css`

#### `jquery_plugins/sticky`

- Purpose: sticky UI elements.
- JS:
  - `js/jquery.sticky.js`

#### `jquery_plugins/simplePagination`

- Purpose: pagination UI helper.
- JS:
  - `simplePagination/jquery.simplePagination.js`
- Dependencies:
  - `system/jquery`

### 3) Vendor asset directories

In addition to the explicitly-defined Drupal libraries above, the module ships additional JS/CSS/vendor assets that are referenced directly by other modules.

Notable directories:

- `data_tables/`
  - Includes DataTables distribution files (e.g. `data_tables/1.13.4/js/jquery.dataTables1.13.4.min.js`).
  - Other modules may reference these files directly in their own libraries.

- `js/`
  - Contains various jQuery and jQuery UI related JS files.
  - Not all of these are currently exported as Drupal libraries in `jquery_plugins.libraries.yml`.

## Installation

1. Enable the module:

- **Via UI**: Extend page
- **Via Drush** (if available):
  - `drush en jquery_plugins`

2. Clear caches:

- Admin UI: Performance page
- Or via Drush: `drush cr`

## Configuration

There is no Drupal configuration UI for this module.

Configuration is done by:

- Adding/removing assets within the module directory.
- Declaring/adjusting libraries in `jquery_plugins.libraries.yml`.

Important behaviors from the library definitions:

- Several libraries are marked `every_page: true` and use a low `group: -100`, meaning they may be loaded broadly and early.

## Usage

### 1) Attach a library in PHP render arrays

Example pattern:

- Add to a render array:
  - `#attached['library'][] = 'jquery_plugins/jScrollPane'`

### 2) Attach a library in Twig

Example pattern:

- `{{ attach_library('jquery_plugins/chosen') }}`

### 3) Referencing bundled assets in other module libraries

Some custom modules reference files under `modules/custom/jquery_plugins/...` directly (for example, DataTables assets) in their own `.libraries.yml`.

If you change file paths or upgrade vendor versions, you must update those consuming libraries accordingly.

## Testing

### Manual Testing

1. **Scenario 1: Library attachment loads JS/CSS**
  - Description: Verify that attaching a library includes the expected assets.
  - Steps:
    1. Find a page that attaches `jquery_plugins/jScrollPane` (or attach it temporarily).
    2. Inspect the page source / network tab.
  - Expected Result: The library’s JS/CSS files are loaded.

2. **Scenario 2: Consuming module behavior works**
  - Description: Verify plugins still function in the UIs that depend on them.
  - Steps:
    1. Navigate to a page using DataTables / chosen / custom scrollbar.
  - Expected Result: UI enhancements are present and no JS console errors occur.

### Automated Testing

No automated tests are included in this module currently.
