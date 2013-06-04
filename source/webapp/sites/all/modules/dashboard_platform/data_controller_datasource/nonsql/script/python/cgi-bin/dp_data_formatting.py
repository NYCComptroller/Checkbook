# ##############################################################################
#   Transpose columns to rows formatter
#       Input:
#           {c1: test1, c2: 1.17, c3: 0.13}
#           {c1: test2,           c3: 0.23}
#           {c1: test3, c2: 3.17, c3: 0.33}
#           {c1: test4, c2: 4.17, c3: 0.43}
#       Output (keyColumnNames: [c1], selectedNonkeyColumns: None | [c2, c3], generatedColumnName4Enumeration: category, generatedColumnName4NonkeyColumns: value):
#           {c1: test1, category: c2, value: 1.17}
#           {c1: test1, category: c3, value: 0.13}
#           {c1: test2, category: c2, value: 2.17}
#           {c1: test2, category: c3, value: 0.23}
#           {c1: test3, category: c2, value: 3.17}
#           {c1: test3, category: c3, value: 0.33}
#           {c1: test4, category: c2, value: 4.17}
#           {c1: test4, category: c3, value: 0.43}
# ##############################################################################
def transposeRecordColumns2Rows(record, keyColumnNames, selectedNonkeyColumns, generatedColumnName4Enumeration, generatedColumnName4NonkeyColumns):
    recordSubset4KeyColumnNames = dict()
    for columnName in keyColumnNames:
        recordSubset4KeyColumnNames[columnName] = record[columnName] if (columnName in record) else None

    transposedRecords = list()
    for columnName, value in record.iteritems():
        if (columnName in keyColumnNames):
            continue
        if (value is None):
            continue
        if ((selectedNonkeyColumns is not None) and (columnName not in selectedNonkeyColumns)):
            continue

        transposedRecord = recordSubset4KeyColumnNames.copy()
        transposedRecord[generatedColumnName4Enumeration] = columnName
        transposedRecord[generatedColumnName4NonkeyColumns] = value

        transposedRecords.append(transposedRecord)

    return transposedRecords


def transposeColumns2Rows(records, keyColumnNames, selectedNonkeyColumns, generatedColumnName4Enumeration, generatedColumnName4NonkeyColumns):
    if (records is None):
        return None

    transposedRecords = list()
    for record in records:
        transposedRecords.extend(transposeRecordColumns2Rows(record, keyColumnNames, selectedNonkeyColumns, generatedColumnName4Enumeration, generatedColumnName4NonkeyColumns))

    return transposedRecords


# ##############################################################################
#   Table formatter:
#       Input:
#           {c1: test1, c2: 10, c3: 0.13}
#           {c1: test2,         c3: 0.23}
#           {c1: test3, c2: 30, c3: 0.33}
#           {c1: test4, None,   c3: 0.43}
#       Output:
#           [c1,    c2,   c3  ]
#           [test1, 10,   0.13]
#           [test2, None, 0.23]
#           [test3, 30,   0.33]
#           [test4, None, 0.43]
# ##############################################################################
def transform2Table(records, selectedColumns = None):
    if (records is None):
        return None

    header = list()
    if (selectedColumns is not None):
        header.extend(selectedColumns)

    table = list()

    headerColumnIndexes = dict()
    for record in records:
        tableRecord = list()
        if (selectedColumns is None):
            for columnName, value in record.iteritems():
                if (columnName not in headerColumnIndexes):
                    index = len(headerColumnIndexes)
                    headerColumnIndexes[columnName] = index

                    # adding NULL values for new column for previous records
                    header.append(columnName)
                    for r in table:
                        r.insert(index, None)

            for columnName, index in headerColumnIndexes.iteritems():
                tableRecord.insert(index, record[columnName] if (columnName in record) else None)
        else:
            # using preselected column name
            for columnName in selectedColumns:
                tableRecord.append(record[columnName] if (columnName in record) else None)

        table.append(tableRecord)

    table.insert(0, header)

    return table
