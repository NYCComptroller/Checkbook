# Checkbook API

RESTful API for programmatic access to NYC Checkbook financial data with CSV/XML export and queue processing.

## Table of Contents

- [Module Purpose](#module-purpose)
- [Module Functionality](#module-functionality)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Testing](#testing)

## Module Purpose

Checkbook API provides RESTful endpoints for external applications to access NYC financial transparency data. Enables programmatic data retrieval, bulk exports, and integration with third-party systems.

Key capabilities:
- Query financial data across all domains (Budget, Spending, Contracts, Payroll, Revenue)
- Export large datasets in CSV/XML formats
- Queue processing for big data requests
- Multi-datasource support (Citywide, NYCHA, EDC/OGE)
- Rate limiting and request validation
- Email delivery for queued exports

## Module Functionality

### Core Features

#### API Endpoints
- **Main API**: `/api` - Primary endpoint for all requests
- **JSON API**: JSON-formatted responses
- **CSV Export**: Comma-separated values format
- **XML Export**: XML-formatted output
- **Queue System**: Async processing for large datasets

#### Supported Domains
- **Budget**: Adopted/modified budgets, expense categories
- **Spending**: Vendor payments, purchase orders, OPA transactions
- **Contracts**: Active/registered contracts, master agreements
- **Payroll**: Employee salaries, overtime, titles
- **Revenue**: Revenue sources, fund classes, categories

#### Data Sources
- **Citywide**: Primary NYC financial data
- **NYCHA**: NYC Housing Authority data
- **EDC/OGE**: Economic Development Corporation data

### Technical Components

#### Core Classes
- **CheckBookAPI** (`src/API/CheckBookAPI.php`): Main API interface, handles requests
- **CheckbookAPIService** (`src/API/CheckbookAPIService.php`): Service layer
- **CheckbookAPIRepository** (`src/API/CheckbookAPIRepository.php`): Data access
- **CheckbookAPIEntity** (`src/API/CheckbookAPIEntity.php`): Entity representation
- **ResponseStatus** (`src/API/ResponseStatus.php`): Status codes

#### Request Handling
- **AbstractAPISearchCriteria** (`src/Criteria/`): Base criteria class
- **Domain-specific criteria**: Budget, Spending, Contracts, Payroll, Revenue
- **Validation**: Input validation and sanitization

#### Data Handlers
- **CSVDataHandler** (`src/Handler/CSVDataHandler.php`): CSV export
- **XMLDataHandler** (`src/Handler/XMLDataHandler.php`): XML export
- **JSONDataHandler** (`src/Handler/JSONDataHandler.php`): JSON responses
- **HTMLDataHandler** (`src/Handler/HTMLDataHandler.php`): HTML documentation

#### Queue System
- **QueueUtil** (`src/Queue/QueueUtil.php`): Queue management
- **QueueProcessor**: Async job processing
- **QueueEmailer**: Email delivery for completed exports

#### Configuration
JSON config files in `src/config/`:
- `budget.json`, `budget_nycha.json`
- `spending.json`, `spending_nycha.json`, `spending_oge.json`
- `contracts.json`, `contracts_nycha.json`, `contracts_oge.json`
- `payroll.json`, `payroll_nycha.json`
- `revenue.json`, `revenue_nycha.json`

#### Utilities
- **ConfigUtil** (`src/config/ConfigUtil.php`): Config loading
- **Formatters** (`src/Formatter/`): Data formatting
- **EventSubscriber**: Request/response events

#### Documentation
- **HTMLDocumentation** (`src/HTMLDocumentation/`): Auto-generated API docs
- **JsonAPIDocs** (`src/JsonAPIDocs/`): JSON API documentation
- **SampleInputSchema** (`src/SampleInputSchema/`): Example requests

## Installation

### Dependencies
- Drupal 10 or 11
- `checkbook_project` module (optional, commented out)
- `checkbook_log` module (for logging)
- `data_controller` module (for data access)

### Installation Steps

```bash
# Enable module
drush en checkbook_api

# Clear cache
drush cr

# Verify endpoint
curl http://localhost/api
```

### Queue Processing Setup

```bash
# Add cron job for queue processing
0 * * * * cd /var/www/html && drush queue:run checkbook_api_queue
```

## Configuration

### API Endpoint
Base URL: `/api`

### Request Format

**POST Request**:
```json
{
  "global": {
    "type_of_data": "Spending",
    "records_from": 1,
    "max_records": 100,
    "response_format": "csv"
  },
  "search_criteria": {
    "fiscal_year": "2024",
    "agency_code": "057"
  }
}
```

### Parameters

#### Global Parameters
- `type_of_data`: Domain (Budget, Spending, Contracts, Payroll, Revenue)
- `records_from`: Starting record (pagination)
- `max_records`: Maximum records (limit: 1000 for direct, unlimited for queue)
- `response_format`: Output format (csv, xml, json)
- `datasource`: Data source (checkbook, nycha, oge)

#### Search Criteria
Domain-specific filters (see config JSON files for available fields)

### Response Formats

**CSV**: Comma-separated values with headers
**XML**: Structured XML with root element
**JSON**: JSON array of objects

### Queue Processing

For large datasets (>1000 records):
```json
{
  "global": {
    "type_of_data": "Spending",
    "max_records": 50000,
    "response_format": "csv",
    "queue": true,
    "email": "user@example.com"
  }
}
```

Response includes token for tracking.

## Usage

### Basic API Request

```bash
# CSV export
curl -X POST http://localhost/api \
  -H "Content-Type: application/json" \
  -d '{
    "global": {
      "type_of_data": "Spending",
      "records_from": 1,
      "max_records": 100,
      "response_format": "csv"
    },
    "search_criteria": {
      "fiscal_year": "2024"
    }
  }'
```

### XML Export

```bash
curl -X POST http://localhost/api \
  -H "Content-Type: application/json" \
  -d '{
    "global": {
      "type_of_data": "Contracts",
      "max_records": 50,
      "response_format": "xml"
    },
    "search_criteria": {
      "contract_status": "registered"
    }
  }'
```

### Queue Request

```bash
curl -X POST http://localhost/api \
  -H "Content-Type: application/json" \
  -d '{
    "global": {
      "type_of_data": "Payroll",
      "max_records": 100000,
      "response_format": "csv",
      "queue": true,
      "email": "user@example.com"
    },
    "search_criteria": {
      "fiscal_year": "2024"
    }
  }'
```

Response: `{"token": "abc123xyz", "status": "queued"}`

### Domain-Specific Examples

#### Budget
```json
{
  "global": {"type_of_data": "Budget", "response_format": "csv"},
  "search_criteria": {
    "fiscal_year": "2024",
    "agency_code": "057",
    "expense_category": "PS"
  }
}
```

#### Spending
```json
{
  "global": {"type_of_data": "Spending", "response_format": "csv"},
  "search_criteria": {
    "fiscal_year": "2024",
    "vendor_name": "IBM",
    "amount_from": "10000"
  }
}
```

#### Contracts
```json
{
  "global": {"type_of_data": "Contracts", "response_format": "xml"},
  "search_criteria": {
    "contract_status": "registered",
    "award_method": "competitive"
  }
}
```

### NYCHA Data

```json
{
  "global": {
    "type_of_data": "Spending",
    "datasource": "nycha",
    "response_format": "csv"
  },
  "search_criteria": {
    "fiscal_year": "2024"
  }
}
```

### Pagination

```json
{
  "global": {
    "records_from": 101,
    "max_records": 100
  }
}
```

## Testing

### Manual Testing

#### Scenario 1: Basic CSV Export
- **Steps**: POST request with 100 records, CSV format
- **Expected**: CSV file with headers and data

#### Scenario 2: XML Export
- **Steps**: POST request with XML format
- **Expected**: Valid XML response

#### Scenario 3: Pagination
- **Steps**: Request records 1-100, then 101-200
- **Expected**: Different result sets

#### Scenario 4: Queue Processing
- **Steps**: Request 10,000 records with email
- **Expected**: Token returned, email received when complete

#### Scenario 5: Invalid Request
- **Steps**: POST with invalid domain
- **Expected**: Error response with details

#### Scenario 6: Max Records Limit
- **Steps**: Request 2000 records without queue
- **Expected**: Error or truncated to 1000

#### Scenario 7: NYCHA Data
- **Steps**: Request with datasource=nycha
- **Expected**: NYCHA-specific data returned

#### Scenario 8: Search Criteria
- **Steps**: Filter by fiscal year, agency
- **Expected**: Filtered results only

#### Scenario 9: Empty Results
- **Steps**: Query with no matching data
- **Expected**: Empty dataset, no errors

#### Scenario 10: Queue Email Delivery
- **Steps**: Queue request, wait for processing
- **Expected**: Email with download link

### Testing Checklist
- [ ] API endpoint responds
- [ ] CSV export works
- [ ] XML export works
- [ ] JSON response valid
- [ ] Pagination functions
- [ ] Queue system processes requests
- [ ] Email delivery works
- [ ] Search filters apply correctly
- [ ] NYCHA datasource accessible
- [ ] EDC/OGE datasource accessible
- [ ] Error handling works
- [ ] Rate limiting enforced
- [ ] Large datasets handled
- [ ] Invalid requests rejected
- [ ] Documentation accessible

### Debugging

```bash
# Check logs
drush watchdog:show --type=checkbook_api

# Test queue
drush queue:list
drush queue:run checkbook_api_queue

# Validate JSON
cat request.json | jq .
```

## Best Practices

1. **Use Queue for Large Datasets**: >1000 records
2. **Implement Pagination**: Don't request all data at once
3. **Cache Responses**: Cache frequently requested data
4. **Validate Input**: Always validate request parameters
5. **Monitor Usage**: Track API usage and performance
6. **Rate Limiting**: Implement rate limits for production
7. **Error Handling**: Handle errors gracefully
8. **Documentation**: Keep API docs updated

## Related Modules

- **checkbook_project**: Core utilities
- **checkbook_log**: Logging
- **data_controller**: Data access layer
- **checkbook_datafeeds**: Similar export functionality

## Support

For API issues:
- Review config files in `src/config/`
- Check sample schemas in `src/SampleInputSchema/`
- View HTML documentation at `/api` (if enabled)
- Check logs for errors
- Consult development team
