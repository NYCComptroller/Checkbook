import dp_operator


# ##############################################################################
#   Processing filter configuration
# ##############################################################################
FILTER__COLUMN_NAME = 'n'
FILTER__OPERATOR_NAME = 'o'
FILTER__OPERATOR_VALUE = 'v'


class Filter(object):

    columnName = None
    operator = None

    def __init__(self, columnName, operator):
        self.columnName = columnName
        self.operator = operator


def parseFilters(filterConfigs):
    if (filterConfigs is None):
        return None

    # 'fixing' missing properties with possible default values
    for filterConfig in filterConfigs:
        if (FILTER__OPERATOR_NAME not in filterConfig):
            filterConfig[FILTER__OPERATOR_NAME] = dp_operator.OPERATOR_NAME__EQUAL

    filters = list()
    for filterConfig in filterConfigs:
        operatorName = filterConfig[FILTER__OPERATOR_NAME]
        operatorParameters = filterConfig[FILTER__OPERATOR_VALUE] if (FILTER__OPERATOR_VALUE in filterConfig) else None
        operator = dp_operator.initiateOperator(operatorName, operatorParameters)

        columnName = filterConfig[FILTER__COLUMN_NAME]
        filters.append(Filter(columnName, operator))

    return filters


def serializeFilters(filters):
    filterConfigs = list()

    if (filters is not None):
        for filter in filters:
            filterConfigs.append({FILTER__COLUMN_NAME: filter.columnName, FILTER__OPERATOR_NAME: filter.operator.name, FILTER__OPERATOR_VALUE: filter.operator.getParameters()})

    return filterConfigs if (len(filterConfigs) > 0) else None


def selectFilters(filters, columnNames):
    selectedFilters = list()

    if (filters is not None):
        for filter in filters:
            if (filter.columnName in columnNames):
                selectedFilters.append(filter)

    return selectedFilters if (len(selectedFilters) > 0) else None


def containsFilterColumns(filters, columnNames):
    if (filters is not None):
        for filter in filters:
            if (filter.columnName in columnNames):
                return True

    return False


def excludeFilters(filters, excludableColumnNames):
    selectedFilters = list()

    if (filters is not None):
        for filter in filters:
            if (filter.columnName not in excludableColumnNames):
                selectedFilters.append(filter)

    return selectedFilters if (len(selectedFilters) > 0) else None


def getFilterColumnNames(filters):
    filterColumnNames = list()

    if (filters is not None):
        for filter in filters:
            if (filter.columnName not in filterColumnNames):
                filterColumnNames.append(filter.columnName)

    return filterColumnNames if (len(filterColumnNames) > 0) else None


# ##############################################################################
#   Filtering records
# ##############################################################################
def isRecordApplicable(record, filters):
    if (filters is not None):
        for filter in filters:
            value = record[filter.columnName] if (filter.columnName in record) else None
            if (filter.operator.check(value) == False):
                return False

    return True


def applyFilters(data, filters):
    result = False
    
    if ((data is not None) and (filters is not None)):
        for i in range(len(data) - 1, 0 - 1, -1):
            if (isRecordApplicable(data[i], filters) == False):
                del data[i]
                result = True

    return result


