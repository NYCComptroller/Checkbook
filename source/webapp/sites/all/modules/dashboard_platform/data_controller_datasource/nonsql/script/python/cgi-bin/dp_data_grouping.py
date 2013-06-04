import __builtin__

AGGREGATION_STATE__PROCESS_RECORD = 0
AGGREGATION_STATE__FINALIZE = 1

def groupBy(records, groupByColumnNames, functions = None):
    if (records is None):
        return None

    if (isinstance(groupByColumnNames, str)):
        groupByColumnNames = [groupByColumnNames]

    delimiter = ':-(g)'

    recordIndexes = dict()

    result = list()
    for record in records:
        groupByRecord = dict()

        key = 'groupBy'
        if (groupByColumnNames is not None):
            for columnName in groupByColumnNames:
                v = None
                if (columnName in record):
                    v = record[columnName]
                    if (isinstance(v, int)):
                        v = str(v)
                    groupByRecord[columnName] = v
                else:
                    groupByRecord[columnName] = None
                key += delimiter + ('None' if (v is None) else v)

        recordIndex = recordIndexes[key] if (key in recordIndexes) else None
        if (recordIndex is None):
            recordIndex = len(recordIndexes)
            recordIndexes[key] = recordIndex
            result.append(groupByRecord)
        else:
            groupByRecord = result[recordIndex]

        if (functions is not None):
            for columnName, func in functions.iteritems():
                func(groupByRecord, columnName, AGGREGATION_STATE__PROCESS_RECORD, record)

    if (functions is not None):
        for groupByRecord in result:
            for columnName, func in functions.iteritems():
                func(groupByRecord, columnName, AGGREGATION_STATE__FINALIZE)

    return result


def sum(columnName, distinct = False):
    def implementation(groupByRecord, resultColumnName, state, record = None):
        if (state == AGGREGATION_STATE__PROCESS_RECORD):
            if (resultColumnName not in groupByRecord):
                groupByRecord[resultColumnName] = dict()
                if (distinct):
                    groupByRecord[resultColumnName]['list'] = list()

            v = record[columnName] if (columnName in record) else None
            if (v is not None):
                eligible = True
                if (distinct):
                    if (v in groupByRecord[resultColumnName]['list']):
                        eligible = False
                    else:
                        groupByRecord[resultColumnName]['list'].append(v)
                if (eligible):
                    groupByRecord[resultColumnName]['value'] = (groupByRecord[resultColumnName]['value'] if ('value' in groupByRecord[resultColumnName]) else 0) + v
        elif (state == AGGREGATION_STATE__FINALIZE):
            v = None
            if ((resultColumnName in groupByRecord) and ('value' in groupByRecord[resultColumnName])):
                v = groupByRecord[resultColumnName]['value']
            groupByRecord[resultColumnName] = v

    return implementation


def avg(columnName, distinct = False):
    def implementation(groupByRecord, resultColumnName, state, record = None):
        if (state == AGGREGATION_STATE__PROCESS_RECORD):
            if (resultColumnName not in groupByRecord):
                groupByRecord[resultColumnName] = dict()
                if (distinct):
                    groupByRecord[resultColumnName]['list'] = list()

            v = record[columnName] if (columnName in record) else None
            if (v is not None):
                eligible = True
                if (distinct):
                    if (v in groupByRecord[resultColumnName]['list']):
                        eligible = False
                    else:
                        groupByRecord[resultColumnName]['list'].append(v)
                if (eligible):
                    groupByRecord[resultColumnName]['numerator'] = (groupByRecord[resultColumnName]['numerator'] if ('numerator' in groupByRecord[resultColumnName]) else 0.0) + v
                    groupByRecord[resultColumnName]['denominator'] = (groupByRecord[resultColumnName]['denominator'] if ('denominator' in groupByRecord[resultColumnName]) else 0) + 1
        elif (state == AGGREGATION_STATE__FINALIZE):
            v = None
            if ((resultColumnName in groupByRecord) and ('numerator' in groupByRecord[resultColumnName])):
                v = groupByRecord[resultColumnName]['numerator'] / groupByRecord[resultColumnName]['denominator']
            groupByRecord[resultColumnName] = v

    return implementation


def min(columnName):
    def implementation(groupByRecord, resultColumnName, state, record = None):
        if (state == AGGREGATION_STATE__PROCESS_RECORD):
            v = record[columnName] if (columnName in record) else None
            if (v is not None):
                groupByRecord[resultColumnName] = __builtin__.min(v, groupByRecord[resultColumnName]) if (resultColumnName in groupByRecord) else v

    return implementation


def max(columnName):
    def implementation(groupByRecord, resultColumnName, state, record = None):
        if (state == AGGREGATION_STATE__PROCESS_RECORD):
            v = record[columnName] if (columnName in record) else None
            if (v is not None):
                groupByRecord[resultColumnName] = __builtin__.max(v, groupByRecord[resultColumnName]) if (resultColumnName in groupByRecord) else v

    return implementation


def count(columnName = None, distinct = False):
    if (columnName == '*'):
        columnName = None

    def implementation(groupByRecord, resultColumnName, state, record = None):
        if (state == AGGREGATION_STATE__PROCESS_RECORD):
            if (resultColumnName not in groupByRecord):
                groupByRecord[resultColumnName] = dict()
                groupByRecord[resultColumnName]['value'] = 0
                if (distinct):
                    groupByRecord[resultColumnName]['list'] = list()

            eligible = True
            if (columnName is not None):
                v = record[columnName] if (columnName in record) else None
                if (v is None):
                    eligible = False
                elif (distinct):
                    if (v in groupByRecord[resultColumnName]['list']):
                        eligible = False
                    else:
                        groupByRecord[resultColumnName]['list'].append(v)
            if (eligible):
                groupByRecord[resultColumnName]['value'] = groupByRecord[resultColumnName]['value'] + 1
        elif (state == AGGREGATION_STATE__FINALIZE):
            v = 0
            if ((resultColumnName in groupByRecord) and ('value' in groupByRecord[resultColumnName])):
                v = groupByRecord[resultColumnName]['value']
            groupByRecord[resultColumnName] = v

    return implementation
