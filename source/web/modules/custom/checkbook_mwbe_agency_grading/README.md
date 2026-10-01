# Checkbook MWBE Agency Grading

M/WBE (Minority/Women-owned Business Enterprise) agency performance grading system with visual scorecards and CSV export.

## Table of Contents

- [Module Purpose](#module-purpose)
- [Module Functionality](#module-functionality)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Testing](#testing)

## Module Purpose

Provides visual grading and reporting for NYC agency performance in M/WBE contracting. Displays agency rankings, spending breakdowns by M/WBE category, and performance metrics.

Key capabilities:
- Agency M/WBE spending rankings
- Prime vendor and subvendor data views
- M/WBE category breakdowns (Black American, Hispanic American, Asian American, Women, etc.)
- Visual grading interface
- CSV export of grading data
- Fiscal year filtering
- Interactive agency selection

## Module Functionality

### Core Features

#### Agency Grading
- **Performance Rankings**: Agencies ranked by M/WBE spending
- **Category Breakdown**: Spending by M/WBE category
- **Prime Vendor View**: Prime vendor M/WBE spending
- **Subvendor View**: Subvendor M/WBE spending
- **Fiscal Year Comparison**: Year-over-year performance

#### M/WBE Categories
- **Black American**: Spending with Black-owned businesses
- **Hispanic American**: Spending with Hispanic-owned businesses
- **Asian American**: Spending with Asian-owned businesses
- **Native American**: Spending with Native American-owned businesses
- **Women (Non-Minority)**: Spending with women-owned businesses
- **Emerging (Non-Minority)**: Emerging business enterprises
- **Total M/WBE**: Combined M/WBE spending

#### Visual Interface
- **Left Panel**: Agency list sorted by M/WBE spending
- **Main Panel**: Detailed grading data and charts
- **Interactive**: Click agency to view details
- **Responsive**: Mobile-friendly design

#### Export
- **CSV Export**: Download grading data
- **Filtered Export**: Export respects current filters
- **Complete Data**: All M/WBE categories included

### Technical Components

#### Controller
- **DefaultController** (`src/Controller/DefaultController.php`):
  - `_checkbook_mwbe_agency_grading()`: Main grading page
  - `_checkbook_mwbe_agency_grading_csv()`: CSV export

#### Routes
- **Prime Vendor Data**: `/mwbe_agency_grading/prime_vendor_data/year/{filters}`
- **Subvendor Data**: `/mwbe_agency_grading/sub_vendor_data/year/{filters}`
- **CSV Export**: `/mwbe_agency_grading_csv/{filters}`

#### Template
- **mwbe_agency_grading_main.html.twig** (`templates/`): Main grading interface

#### JavaScript
- **mwbe_agency_grading.js**: Interactive features, agency selection, filtering

#### CSS
- **mwbe.css**: Grading interface styling, color coding, layout

#### Twig Extension
- **MWBEAgencyGradingExtension** (`src/Twig/`): Custom Twig functions

#### Helper Functions
- `_checkbook_mwbe_agency_grading_param_update()`: Parameter processing
- `_checkbook_mwbe_agency_grading_left()`: Left panel agency list
- `_checkbook_agency_grading_comp()`: Agency sorting by spending
- `_mwbe_agency_grading_current_cats()`: Current M/WBE categories

### Data Processing

1. **Query M/WBE Data**: Retrieve agency M/WBE spending
2. **Category Breakdown**: Split by M/WBE category
3. **Calculate Totals**: Sum spending per agency
4. **Sort Agencies**: Rank by total M/WBE spending
5. **Render Interface**: Display grading scorecard
6. **Export Option**: Generate CSV download

## Installation

### Dependencies
- Drupal 10 or 11
- `checkbook_infrastructure_layer` (utilities)
- `checkbook_project` (core functionality)

### Installation Steps

```bash
# Enable module
drush en checkbook_mwbe_agency_grading

# Clear cache
drush cr

# Verify routes
drush route:list | grep mwbe_agency_grading
```

## Configuration

### Routes Configuration

**Prime Vendor Grading**:
```
/mwbe_agency_grading/prime_vendor_data/year/2024
```

**Subvendor Grading**:
```
/mwbe_agency_grading/sub_vendor_data/year/2024
```

**CSV Export**:
```
/mwbe_agency_grading_csv/year/2024
```

### M/WBE Categories

Categories tracked:
- Black American (minority_type_id: 2)
- Hispanic American (minority_type_id: 3)
- Asian American (minority_type_id: 4, 5, 10)
- Native American (minority_type_id: 6)
- Women Non-Minority (minority_type_id: 9)
- Emerging Non-Minority (minority_type_id: 99)
- Total M/WBE (combined)

### Template Variables

Template receives:
- `left_content`: Agency list with spending totals
- `nyc_data`: Detailed grading data
- `params`: Current filter parameters

## Usage

### Viewing Agency Grading

**Prime Vendor View**:
```
/mwbe_agency_grading/prime_vendor_data/year/2024
```

Shows:
- Agency rankings
- M/WBE spending by category
- Prime vendor relationships
- Performance metrics

**Subvendor View**:
```
/mwbe_agency_grading/sub_vendor_data/year/2024
```

Shows:
- Subvendor M/WBE spending
- Prime-to-sub relationships
- Category breakdowns

### Filtering by Fiscal Year

```
# FY 2024
/mwbe_agency_grading/prime_vendor_data/year/2024

# FY 2023
/mwbe_agency_grading/prime_vendor_data/year/2023
```

### Agency Selection

1. **View List**: Left panel shows agencies sorted by M/WBE spending
2. **Click Agency**: Select agency to view details
3. **View Details**: Main panel shows detailed breakdown
4. **Compare**: Switch between agencies

### Exporting Data

**CSV Export**:
```
/mwbe_agency_grading_csv/year/2024
```

CSV includes:
- Agency name
- M/WBE category spending
- Total M/WBE spending
- Percentage breakdowns

### Interface Layout

```
┌─────────────────────────────────────────┐
│  M/WBE Agency Grading - FY 2024        │
├──────────────┬──────────────────────────┤
│ Agencies     │  Grading Details         │
│ (sorted by   │                          │
│  M/WBE $)    │  Agency: Police Dept     │
│              │                          │
│ 1. Police    │  Black American: $10M    │
│ 2. Fire      │  Hispanic: $8M           │
│ 3. Education │  Asian: $5M              │
│ 4. ...       │  Women: $12M             │
│              │  Total M/WBE: $35M       │
│              │                          │
│              │  [Export CSV]            │
└──────────────┴──────────────────────────┘
```

### Programmatic Access

```php
use Drupal\checkbook_mwbe_agency_grading\Controller\DefaultController;

$controller = new DefaultController();

// Get grading data
$build = $controller->_checkbook_mwbe_agency_grading('2024');

// Export CSV
$csv = $controller->_checkbook_mwbe_agency_grading_csv('year/2024');
```

## Testing

### Manual Testing

#### Scenario 1: Prime Vendor Grading
- **Steps**: Navigate to prime vendor grading page
- **Expected**: Agency rankings display

#### Scenario 2: Subvendor Grading
- **Steps**: Navigate to subvendor grading page
- **Expected**: Subvendor data displays

#### Scenario 3: Agency Selection
- **Steps**: Click agency in left panel
- **Expected**: Details update in main panel

#### Scenario 4: Fiscal Year Filter
- **Steps**: Change fiscal year parameter
- **Expected**: Data updates for selected year

#### Scenario 5: Category Breakdown
- **Steps**: View agency details
- **Expected**: All M/WBE categories shown

#### Scenario 6: CSV Export
- **Steps**: Click export CSV
- **Expected**: CSV file downloads

#### Scenario 7: Agency Sorting
- **Steps**: Check agency list order
- **Expected**: Sorted by M/WBE spending (highest first)

#### Scenario 8: Total Calculation
- **Steps**: Verify M/WBE totals
- **Expected**: Totals match category sums

#### Scenario 9: Visual Grading
- **Steps**: Check grading display
- **Expected**: Clear visual indicators

#### Scenario 10: Mobile View
- **Steps**: View on mobile device
- **Expected**: Responsive layout works

### Testing Checklist
- [ ] Prime vendor page loads
- [ ] Subvendor page loads
- [ ] Agency list displays
- [ ] Agencies sorted correctly
- [ ] Category breakdowns accurate
- [ ] Totals calculated correctly
- [ ] Agency selection works
- [ ] Fiscal year filter works
- [ ] CSV export works
- [ ] CSV data accurate
- [ ] Visual grading clear
- [ ] Responsive design works
- [ ] JavaScript interactions work
- [ ] No console errors
- [ ] Performance acceptable

### Debugging

```bash
# Check routes
drush route:list | grep mwbe_agency_grading

# Test grading page
curl http://localhost/mwbe_agency_grading/prime_vendor_data/year/2024

# Test CSV export
curl http://localhost/mwbe_agency_grading_csv/year/2024 > grading.csv

# Check logs
drush watchdog:show --type=checkbook_mwbe_agency_grading

# Verify JavaScript
# Browser DevTools > Console
```

## Best Practices

1. **Data Accuracy**: Verify M/WBE category mappings
2. **Performance**: Cache grading data when possible
3. **Export Format**: Ensure CSV properly formatted
4. **Visual Design**: Use clear color coding for grades
5. **Accessibility**: Ensure keyboard navigation works
6. **Mobile**: Test on various screen sizes
7. **Documentation**: Document grading criteria
8. **Updates**: Keep M/WBE categories current

## Related Modules

- **checkbook_project**: Core M/WBE functionality
- **checkbook_infrastructure_layer**: Utilities
- **checkbook_spending**: Spending data source
- **checkbook_contracts**: Contract M/WBE data

## Support

For issues:
- Review controller in `src/Controller/DefaultController.php`
- Check template in `templates/mwbe_agency_grading_main.html.twig`
- Review JavaScript in `js/mwbe_agency_grading.js`
- Check CSS in `css/mwbe.css`
- Verify M/WBE category definitions
- Test data queries
- Check export functionality
- Review agency sorting logic
