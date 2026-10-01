# Checkbook Metamodel

Dataset metadata configurations defining data structures, cubes, dimensions, and measures for all financial domains.

## Table of Contents

- [Module Purpose](#module-purpose)
- [Module Functionality](#module-functionality)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Testing](#testing)

## Module Purpose

Provides comprehensive metadata definitions for all Checkbook datasets. Defines data structures, relationships, dimensions, measures, and queries used by the data_controller module for data access.

Key capabilities:
- Dataset structure definitions (cubes, dimensions, measures)
- Multi-datasource metadata (Citywide, NYCHA, EDC/OGE)
- Domain-specific configurations (Budget, Spending, Contracts, Payroll, Revenue)
- Query definitions and parameters
- Data relationships and joins
- Column mappings and data types

## Module Functionality

### Core Features

#### Metadata Definitions
- **Cube Definitions**: Fact table structures with dimensions and measures
- **Dimension Definitions**: Lookup tables and hierarchies
- **Measure Definitions**: Aggregatable numeric fields
- **Query Definitions**: Pre-defined queries and filters
- **Relationship Definitions**: Foreign key relationships

#### Datasource Support
- **Checkbook (Citywide)**: 86 metadata files
- **Checkbook NYCHA**: 46 metadata files
- **Checkbook OGE**: 28 metadata files

#### Domain Coverage
- **Budget**: Budget cubes, revenue, percent difference
- **Spending**: Disbursement transactions, vendor relationships
- **Contracts**: Agreements, pending contracts, retroactivity
- **Payroll**: Employee data, agency, department, title aggregations
- **Revenue**: Revenue details and classifications

### Technical Components

#### Module Structure

```
checkbook_metamodel/
├── src/
│   └── metamodel/
│       └── metadata/
│           ├── checkbook/        (86 files)
│           ├── checkbook_nycha/  (46 files)
│           └── checkbook_oge/    (28 files)
```

#### Metadata Files

**Naming Convention**: `cube-{dataset_name}.json`

**Example Files**:
- `cube-budget.json`: Budget dataset
- `cube-contracts.json`: Contracts dataset
- `cube-payroll.json`: Payroll dataset
- `cube-spending.json`: Spending dataset
- `cube-revenue.json`: Revenue dataset

#### JSON Structure

Typical metadata file contains:
```json
{
  "dataset": "budget",
  "datasource": "checkbook",
  "columns": [
    {
      "name": "fiscal_year",
      "type": "integer",
      "key": true
    },
    {
      "name": "adopted_amount",
      "type": "number",
      "measure": true
    }
  ],
  "dimensions": [...],
  "measures": [...],
  "relationships": [...]
}
```

#### Integration

Integrates with:
- **data_controller**: Reads metadata for query building
- **checkbook_project**: Uses metadata for data access
- **widget_config**: References datasets in widget configs

### Metadata Types

#### Cube Metadata
- Fact tables with transactional data
- Dimensions for filtering
- Measures for aggregation
- Example: `cube-spending.json`

#### Dimension Metadata
- Reference/lookup tables
- Hierarchical structures
- Example: Agency, Vendor, Fiscal Year

#### Aggregation Metadata
- Pre-aggregated views
- Summary tables
- Example: `cube-payroll_agency.json`

#### Datafeeds Metadata
- Export-specific configurations
- Column mappings for exports
- Example: `cube-contracts_datafeeds.json`

#### Facet Metadata
- Faceted search configurations
- Filter definitions
- Example: `cube-all_agreement_transactions_by_prime_vendor_facet_data.json`

## Installation

### Dependencies
- Drupal 10 or 11
- `data_controller` module (uses metadata)
- `data_controller_datasource_sql` (optional, for SQL datasources)

### Installation Steps

```bash
# Enable module
drush en checkbook_metamodel

# Clear cache
drush cr

# Verify metadata loaded
drush php-eval "print_r(data_controller_get_metamodel()->getDatasets());"
```

## Configuration

### Metadata Location

Metadata files stored in:
```
src/metamodel/metadata/{datasource}/
```

### Hook Implementation

Module implements `checkbook_metamodel_dc_metamodel()`:
```php
function checkbook_metamodel_dc_metamodel() {
  $items[] = array(
    'path' => \Drupal::service('extension.path.resolver')
      ->getPath('module','checkbook_metamodel')
  );
  return $items;
}
```

This tells data_controller where to find metadata files.

### Datasource Directories

- **checkbook/**: NYC Citywide data
- **checkbook_nycha/**: NYCHA data
- **checkbook_oge/**: EDC/OGE data

### File Naming

Files follow pattern: `cube-{name}.json`

Special suffixes:
- `_datafeeds`: Export configurations
- `_facet_data`: Faceted search configs
- `_by_prime_vendor`: Vendor-specific views
- `_agency`: Agency aggregations
- `_month`: Monthly aggregations

## Usage

### Accessing Metadata

```php
// Get metamodel
$metamodel = data_controller_get_metamodel();

// Get dataset
$dataset = $metamodel->getDataset('checkbook:budget');

// Get columns
$columns = $dataset->getColumns();

// Get measures
$measures = $dataset->getMeasures();

// Get dimensions
$dimensions = $dataset->getDimensions();
```

### Querying with Metadata

```php
use Drupal\data_controller\Controller\DataController;

$dataController = DataController::getInstance();

// Query uses metadata to build SQL
$results = $dataController->queryCube(
  'checkbook:budget',
  ['fiscal_year', 'adopted_amount'],
  ['fiscal_year' => 2024]
);
```

### Widget Configuration

Widgets reference datasets:
```json
{
  "dataset": "checkbook:spending",
  "columns": ["vendor_name", "total_amount"]
}
```

### Available Datasets

**Budget**:
- `checkbook:budget`
- `checkbook:budget_revenue`
- `checkbook:budget_percent_difference`

**Spending**:
- `checkbook:spending`
- `checkbook:disbursement_transactions`
- `checkbook:all_disbursement_transactions`

**Contracts**:
- `checkbook:contracts`
- `checkbook:contracts_datafeeds`
- `checkbook:contract_summary`
- `checkbook:contract_retroactivity`

**Payroll**:
- `checkbook:payroll`
- `checkbook:payroll_agency`
- `checkbook:payroll_employee_agency`

**Revenue**:
- `checkbook:revenue`
- `checkbook:revenue_details`

### NYCHA Datasets

Prefix with `checkbook_nycha:`:
- `checkbook_nycha:budget`
- `checkbook_nycha:spending`
- `checkbook_nycha:contracts`
- `checkbook_nycha:payroll`
- `checkbook_nycha:revenue`

### OGE Datasets

Prefix with `checkbook_oge:`:
- `checkbook_oge:spending`
- `checkbook_oge:contracts`

## Testing

### Manual Testing

#### Scenario 1: Metadata Loading
- **Steps**: Enable module, check metamodel
- **Expected**: All datasets loaded

#### Scenario 2: Dataset Access
- **Steps**: Get dataset via metamodel
- **Expected**: Dataset object returned

#### Scenario 3: Column Definitions
- **Steps**: Get columns from dataset
- **Expected**: All columns defined

#### Scenario 4: Query Building
- **Steps**: Query dataset via data_controller
- **Expected**: Query executes successfully

#### Scenario 5: Multi-Datasource
- **Steps**: Access NYC, NYCHA, OGE datasets
- **Expected**: All datasources work

#### Scenario 6: Widget Integration
- **Steps**: Widget references dataset
- **Expected**: Widget loads data correctly

#### Scenario 7: Datafeeds Metadata
- **Steps**: Use datafeeds dataset for export
- **Expected**: Export uses correct columns

#### Scenario 8: Facet Metadata
- **Steps**: Use facet metadata for filters
- **Expected**: Facets work correctly

#### Scenario 9: Relationships
- **Steps**: Query with joins
- **Expected**: Relationships work

#### Scenario 10: Measures
- **Steps**: Aggregate measures
- **Expected**: Aggregations correct

### Testing Checklist
- [ ] Module enables successfully
- [ ] Metadata files load
- [ ] All datasets accessible
- [ ] Columns defined correctly
- [ ] Measures defined correctly
- [ ] Dimensions defined correctly
- [ ] Relationships work
- [ ] Queries execute
- [ ] NYC datasource works
- [ ] NYCHA datasource works
- [ ] OGE datasource works
- [ ] Widget integration works
- [ ] Export integration works
- [ ] Facet integration works
- [ ] No JSON syntax errors

### Debugging

```bash
# Check metadata loading
drush php-eval "print_r(data_controller_get_metamodel()->getDatasets());"

# Get specific dataset
drush php-eval "\$ds = data_controller_get_metamodel()->getDataset('checkbook:budget'); print_r(\$ds->getColumns());"

# Validate JSON files
find src/metamodel/metadata -name "*.json" -exec json_verify {} \;

# Check file count
find src/metamodel/metadata -name "*.json" | wc -l

# List all datasets
drush php-eval "foreach(data_controller_get_metamodel()->getDatasets() as \$name => \$ds) { echo \$name . PHP_EOL; }"
```

## Best Practices

1. **Consistent Naming**: Follow naming conventions for files
2. **Validate JSON**: Ensure all JSON is valid
3. **Document Changes**: Comment metadata changes
4. **Version Control**: Track metadata changes carefully
5. **Test Queries**: Test queries after metadata changes
6. **Backup**: Keep backups before major changes
7. **Review**: Review metadata for accuracy
8. **Performance**: Consider query performance in design

## Related Modules

- **data_controller**: Consumes metadata for queries
- **checkbook_project**: Uses metadata for data access
- **widget_config**: References datasets in configs
- **checkbook_api**: Uses metadata for API queries
- **checkbook_datafeeds**: Uses datafeeds metadata

## Support

For issues:
- Review metadata files in `src/metamodel/metadata/`
- Validate JSON syntax
- Check data_controller integration
- Verify dataset names in queries
- Review column definitions
- Check measure definitions
- Verify relationships
- Test queries with metadata
