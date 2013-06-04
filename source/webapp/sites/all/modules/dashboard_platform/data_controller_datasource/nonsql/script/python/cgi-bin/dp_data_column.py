# ##############################################################################
#   Processing column names
# ##############################################################################
def containsColumnName(columns, columnName):
    return True if (columns is None) else (columnName in columns)


def selectColumnNames(columns, columnNames):
    if (columns is None):
        return None

    selectedColumns = list()
    for name in columnNames:
        if (name in columns):
            selectedColumns.append(name)

    return selectedColumns if (len(selectedColumns) > 0) else None


def excludeColumnNames(columns, excludableColumnNames):
    if (columns is None):
        return None

    selectedColumns = list()
    for name in columns:
        if (name not in excludableColumnNames):
            selectedColumns.append(name)

    return selectedColumns if (len(selectedColumns) > 0) else None


# ##############################################################################
#   Processing column data
# ##############################################################################
def renameColumns(data, columnNameMap):
    if ((data is None) or (columnNameMap is None)):
        return data

    updatedData = list()
    for record in data:
        updatedRecord = dict()
        for columnName, columnValue in record.iteritems():
            updatedColumnName = columnNameMap[columnName] if (columnName in columnNameMap) else columnName
            updatedRecord[updatedColumnName] = columnValue
        updatedData.append(updatedRecord)

    return updatedData


def removeColumnData(data, columnNames):
    result = False
    if ((data is not None) and (columnNames is not None)):
        for record in data:
            for columnName in columnNames:
                if (columnName in record):
                    del record[columnName]
                    result = True
    return result


def retainColumnData(data, columnNames):
    result = False
    if ((data is not None) and (columnNames is not None)):
        for record in data:
            for k in record.keys():
                if (k not in columnNames):
                    del record[k]
                    result = True
    return result
