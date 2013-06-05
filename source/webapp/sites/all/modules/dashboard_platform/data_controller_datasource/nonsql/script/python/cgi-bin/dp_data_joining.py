def checkKeyColumns(keyColumnNames):
    if (keyColumnNames is None):
        raise ValueError('Key column names have not been provided')

    if (not isinstance(keyColumnNames, list)):
        keyColumnNames = [keyColumnNames]

    return keyColumnNames


def hashKey(record, keyColumnNames):
    delimiter = ':-(j)'

    key = 'join'
    for columnName in keyColumnNames:
        v = None
        if (columnName in record):
            v = record[columnName]
            if (isinstance(v, int)):
                v = str(v)
        key += delimiter + ('None' if (v is None) else v)

    return key


def hashData(data, keyColumnNames):
    hash = dict()
    for record in data:
        key = hashKey(record, keyColumnNames)
        if (key in hash):
            hash[key].append(record)
        else:
            hash[key] = [record]

    return hash


def mergeRecords(recordA, recordB):
    result = recordB.copy()
    result.update(recordA)
    return result


def joinInner(dataA, keyColumnNamesA, dataB, keyColumnNamesB):
    if ((dataA is None) or (dataB is None)):
        return None

    keyColumnNamesA = checkKeyColumns(keyColumnNamesA)
    keyColumnNamesB = checkKeyColumns(keyColumnNamesB)

    result = list()

    hashB = hashData(dataB, keyColumnNamesB)

    for recordA in dataA:
        keyA = hashKey(recordA, keyColumnNamesA)
        if (keyA in hashB):
            recordsB = hashB[keyA]
            for recordB in recordsB:
                result.append(mergeRecords(recordA, recordB))

    return result


def joinLeftOuter(dataA, keyColumnNamesA, dataB, keyColumnNamesB):
    if (dataA is None):
        return None
    if (dataB is None):
        return dataA

    keyColumnNamesA = checkKeyColumns(keyColumnNamesA)
    keyColumnNamesB = checkKeyColumns(keyColumnNamesB)

    result = list()

    hashB = hashData(dataB, keyColumnNamesB)

    for recordA in dataA:
        keyA = hashKey(recordA, keyColumnNamesA)
        if (keyA in hashB):
            recordsB = hashB[keyA]
            for recordB in recordsB:
                result.append(mergeRecords(recordA, recordB))
        else:
            result.append(recordA)

    return result


def joinFull(dataA, keyColumnNamesA, dataB, keyColumnNamesB):
    if (dataA is None):
        return dataB
    if (dataB is None):
        return dataA

    keyColumnNamesA = checkKeyColumns(keyColumnNamesA)
    keyColumnNamesB = checkKeyColumns(keyColumnNamesB)

    result = list()

    hashB = hashData(dataB, keyColumnNamesB)

    keysA = dict()
    for recordA in dataA:
        keyA = hashKey(recordA, keyColumnNamesA)
        if (keyA in hashB):
            recordsB = hashB[keyA]
            for recordB in recordsB:
                result.append(mergeRecords(recordA, recordB))
        else:
            result.append(recordA)
        keysA[keyA] = True

    # adding 'missing' records from source 'B'
    for keyB, recordsB in hashB.iteritems():
        if (keyB not in keysA):
            result.extend(recordsB)

    return result


def joinCross(dataA, dataB):
    if (dataA is None):
        return dataB
    if (dataB is None):
        return dataA

    result = list()
    for recordA in dataA:
        for recordB in dataB:
            result.append(mergeRecords(recordA, recordB))

    return result


def joinUnion(dataA, dataB):
    if (dataA is None):
        return dataB
    if (dataB is None):
        return dataA

    result = list()
    result.extend(dataA)
    result.extend(dataB)

    return result
