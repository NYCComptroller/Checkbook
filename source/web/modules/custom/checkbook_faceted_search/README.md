# Checkbook Faceted Search

Dynamic "Narrow Down" filter system with AJAX-powered facets, autocomplete, and pagination for refining transaction data.

## Table of Contents

- [Module Purpose](#module-purpose)
- [Module Functionality](#module-functionality)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Testing](#testing)

## Module Purpose

Provides faceted search interface for filtering large datasets. Users can narrow down results by selecting multiple filter values with real-time updates and result counts.

Key capabilities:
- Multi-select faceted filters
- Real-time result counts per facet
- AJAX-powered updates (no page reload)
- Autocomplete for large facet lists
- Pagination within facets
- Checkbox-based selection
- URL parameter preservation
- Cross-domain support (Budget, Spending, Contracts, Payroll, Revenue)

## Module Functionality

### Core Features

#### Faceted Filters
- **Multi-Select**: Select multiple values per facet
- **Result Counts**: Shows count for each facet value
- **Checked State**: Preserves selected filters
- **Dynamic Updates**: Facets update based on selections
- **Hierarchical**: Supports nested facets

#### AJAX Functionality
- **Widget Updates**: Facets update without page reload
- **Autocomplete**: Search within large facet lists
- **Pagination**: Navigate through many facet values
- **Performance**: Optimized queries for speed

#### Filter Types
- **Agency Filters**: Filter by agency
- **Vendor Filters**: Filter by vendor
- **Category Filters**: Filter by expense/revenue category
- **Fiscal Year Filters**: Filter by year
- **Amount Range Filters**: Filter by dollar amounts
- **Status Filters**: Filter by contract/transaction status
- **Custom Filters**: Domain-specific filters

### Technical Components

#### Controller
- **DefaultController** (`src/Controller/DefaultController.php`):
  - `_checkbook_faceted_search_node_ajax()`: AJAX widget updates
  - `_checkbook_faceted_search_node_autocomplete()`: Autocomplete handler
  - `_checkbook_faceted_search_node_pagination()`: Facet pagination

#### Utilities
- **FacetUtilities** (`src/Utilities/`): Helper functions for facet processing

#### Routes
- **AJAX Facet Data**: `/faceted-search/ajax/widget/{nid}`
- **Autocomplete**: `/faceted-search/ajax/autocomplete/node/{node}`
- **Pagination**: `/faceted-search/ajax/pagination/{nid}`

#### JavaScript
- **checkbook_faceted_search.js**: Client-side facet interactions, AJAX calls, checkbox handling

#### CSS
- **checkbook_faceted_search.css**: Facet styling

#### Templates
- Facet display templates
- Checkbox list templates

#### Event Subscriber
- Request/response event handling

#### Twig Extension
- Custom Twig functions for facet rendering

### Facet Processing

1. **Initial Load**: Facets rendered with counts
2. **User Selection**: User checks facet value
3. **AJAX Request**: Sends selection to server
4. **Query Update**: Server updates query parameters
5. **Facet Refresh**: Facets reload with new counts
6. **Results Update**: Main content updates with filtered data

### URL Parameter Handling

Facet selections stored in URL:
```
/spending?agency=057~068&vendor=12345~67890&year=2024
```

Multiple values separated by `~` (tilde).

## Installation

### Dependencies
- Drupal 10 or 11
- `checkbook_infrastructure_layer` (utilities, constants)
- `checkbook_project` (core utilities)
- `checkbook_log` (logging)
- `data_controller` (data access)

### Installation Steps

```bash
# Enable module
drush en checkbook_faceted_search

# Clear cache
drush cr

# Verify facets appear on pages
```

## Configuration

### Widget Configuration

Facets configured in widget config JSON:
```json
{
  "filterName": "Agency",
  "urlParameterName": "agency",
  "urlParamMap": {"agency": "agency_id"},
  "dataset": "checkbook:spending",
  "columns": ["agency_name", "txcount"],
  "allowZeroValue": false,
  "urlParameterNameType": "eqtext"
}
```

### Configuration Options

- **filterName**: Display name for facet
- **urlParameterName**: URL parameter key
- **urlParamMap**: Maps URL param to dataset column
- **dataset**: Data source for facet values
- **columns**: Columns to retrieve (value, count)
- **allowZeroValue**: Allow zero as valid value
- **urlParameterNameType**: Parameter processing type (capitalize, eqtext)
- **widgetDataFilterLoader**: Custom filter loading logic

### AJAX Endpoints

- **Widget Update**: `/faceted-search/ajax/widget/{nid}`
- **Autocomplete**: `/faceted-search/ajax/autocomplete/node/{node}`
- **Pagination**: `/faceted-search/ajax/pagination/{nid}`

### JavaScript Settings

Passed via `drupalSettings`:
- Widget configurations
- Current selections
- AJAX endpoints

## Usage

### Basic Facet Interaction

1. **View Page**: Navigate to spending/contracts/etc.
2. **See Facets**: Sidebar shows "Narrow Down" filters
3. **Check Value**: Click checkbox next to facet value
4. **Auto-Update**: Page updates with filtered results
5. **Multiple Selections**: Check multiple values
6. **Uncheck**: Click again to remove filter

### Example: Spending Facets

**Agency Facet**:
```
☐ Police Department (1,234)
☐ Fire Department (567)
☐ Dept of Education (890)
```

After selecting Police:
```
☑ Police Department (1,234)
☐ Fire Department (567)
☐ Dept of Education (890)

Vendor Facet updates:
☐ ABC Corp (45)
☐ XYZ Inc (67)
```

### Autocomplete Usage

For facets with many values:
1. **Type**: Start typing in search box
2. **Suggestions**: Matching values appear
3. **Select**: Click suggestion
4. **Apply**: Facet checked, results update

### Pagination

For facets with 100+ values:
1. **Initial**: Shows first 20 values
2. **Load More**: Click "Load More" button
3. **Next Page**: Next 20 values load
4. **Scroll**: Continue loading as needed

### URL Parameters

Facet selections reflected in URL:
```
# Single selection
/spending?agency=057

# Multiple selections
/spending?agency=057~068~072

# Multiple facets
/spending?agency=057&vendor=12345&year=2024
```

### Programmatic Facet Update

```php
// Get facet data
$node = /* widget node */;
$data = _checkbook_faceted_search_update_data($node);

// Returns:
// [
//   'filter_name' => 'Agency',
//   'checked' => [['Police Department', 1234]],
//   'unchecked' => [['Fire Department', 567]]
// ]
```

### JavaScript Integration

```javascript
// Listen for facet change
jQuery('.facet-checkbox').change(function() {
  // Get checked values
  // Update URL
  // Trigger AJAX update
  updateFacets();
});
```

## Testing

### Manual Testing

#### Scenario 1: Single Facet Selection
- **Steps**: Check one facet value
- **Expected**: Results filter, counts update

#### Scenario 2: Multiple Facet Selection
- **Steps**: Check multiple values in one facet
- **Expected**: Results show OR logic (any match)

#### Scenario 3: Cross-Facet Selection
- **Steps**: Check values in multiple facets
- **Expected**: Results show AND logic (all match)

#### Scenario 4: Uncheck Facet
- **Steps**: Uncheck previously selected facet
- **Expected**: Filter removed, results expand

#### Scenario 5: Autocomplete
- **Steps**: Type in facet search box
- **Expected**: Matching suggestions appear

#### Scenario 6: Pagination
- **Steps**: Click "Load More" in facet
- **Expected**: More facet values load

#### Scenario 7: URL Preservation
- **Steps**: Select facets, copy URL, paste in new tab
- **Expected**: Same filters applied

#### Scenario 8: Result Counts
- **Steps**: Check facet counts
- **Expected**: Accurate counts for each value

#### Scenario 9: Zero Values
- **Steps**: Facet with allowZeroValue, select 0
- **Expected**: Zero treated as valid filter

#### Scenario 10: AJAX Performance
- **Steps**: Rapidly check/uncheck facets
- **Expected**: Updates smooth, no errors

### Testing Checklist
- [ ] Facets display correctly
- [ ] Checkboxes work
- [ ] Result counts accurate
- [ ] AJAX updates work
- [ ] No page reloads
- [ ] Multiple selections work
- [ ] Uncheck removes filter
- [ ] Autocomplete works
- [ ] Pagination works
- [ ] URL parameters correct
- [ ] Browser back button works
- [ ] Cross-facet filtering works
- [ ] Zero values handled (if enabled)
- [ ] Special characters escaped
- [ ] Performance acceptable
- [ ] Error handling works

### Debugging

```bash
# Check logs
drush watchdog:show --type=checkbook_faceted_search

# Test AJAX endpoint
curl http://localhost/faceted-search/ajax/widget/123

# Check JavaScript console
# Browser DevTools > Console

# Verify widget config
drush config:get checkbook_project.widgets

# Test autocomplete
curl http://localhost/faceted-search/ajax/autocomplete/node/123?term=police
```

## Best Practices

1. **Limit Facet Values**: Don't show 1000+ values without pagination
2. **Use Autocomplete**: For facets with many values
3. **Cache Counts**: Cache facet counts when possible
4. **Optimize Queries**: Index columns used in facets
5. **Test Performance**: Test with realistic data volumes
6. **Error Handling**: Handle missing/invalid parameters
7. **Accessibility**: Ensure keyboard navigation works
8. **Mobile**: Test facet UI on mobile devices
9. **URL Length**: Monitor URL length with many selections
10. **Clear Filters**: Provide "Clear All" option

## Related Modules

- **checkbook_advanced_search**: Form-based search
- **checkbook_smart_search**: Quick search
- **checkbook_project**: Core utilities
- **checkbook_infrastructure_layer**: Constants, utilities
- **data_controller**: Data access layer

## Support

For issues:
- Review controller in `src/Controller/DefaultController.php`
- Check utilities in `src/Utilities/`
- Review JavaScript in `js/checkbook_faceted_search.js`
- Check widget configurations
- Test AJAX endpoints
- Review URL parameter handling
- Check logs for errors
- Verify data controller queries
